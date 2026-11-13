<?php
/**
 * app/Services/GiphyService.php  -  GIF search through the server, so the API key stays private.
 * rating=pg keeps results suitable for a university site. Set GIPHY_API_KEY in .env to turn this on.
 */
class GiphyService
{
    private const RATING = 'pg';
    private const SAFE_RATINGS = ['g', 'pg'];
    private const CACHE_SECONDS = 300;

    public static function enabled(): bool
    {
        return (string) config('app.giphy.key') !== '';
    }

    /** Search, or trending GIFs when $q is empty. Returns null if GIPHY can't be reached. */
    public static function search(string $q, int $offset = 0): ?array
    {
        $params = ['limit' => 24, 'offset' => $offset, 'rating' => self::RATING];
        if ($q !== '') {
            $params['q'] = $q;
        }
        $path = $q !== '' ? '/gifs/search' : '/gifs/trending';
        $cache = BASE_PATH . '/storage/cache/giphy-' . sha1($path . json_encode($params)) . '.json';
        if (is_file($cache) && filemtime($cache) > time() - self::CACHE_SECONDS) {
            $cached = json_decode((string) file_get_contents($cache), true);
            if (is_array($cached)) {
                return $cached;
            }
        }
        $json = self::request($path, $params);
        if ($json === null || !is_array($json['data'] ?? null)) {
            return null;
        }
        $gifs = array_values(array_filter(array_map([self::class, 'shape'], $json['data'])));
        @file_put_contents($cache, json_encode($gifs), LOCK_EX);
        return $gifs;
    }

    /** One GIF by id. Asked fresh when saving, so a post can't name an unsafe or made-up GIF. */
    public static function find(string $id): ?array
    {
        if (!self::enabled() || !preg_match('/^[A-Za-z0-9]{1,64}$/', $id)) {
            return null;
        }
        $json = self::request('/gifs/' . $id, []);
        $gif = is_array($json['data'] ?? null) ? self::shape($json['data']) : null;
        return $gif !== null && in_array($json['data']['rating'] ?? '', self::SAFE_RATINGS, true) ? $gif : null;
    }

    /** The address GIPHY serves a GIF from. Built here, so only an id is ever stored. */
    public static function url(string $id): string
    {
        return 'https://media.giphy.com/media/' . rawurlencode($id) . '/giphy.gif';
    }

    private static function shape(array $g): ?array
    {
        $id = (string) ($g['id'] ?? '');
        $preview = (string) ($g['images']['fixed_width_small']['url'] ?? $g['images']['fixed_width']['url'] ?? '');
        $host = (string) parse_url($preview, PHP_URL_HOST);
        if (!preg_match('/^[A-Za-z0-9]{1,64}$/', $id) || !str_ends_with($host, '.giphy.com')) {
            return null;
        }
        return [
            'id' => $id,
            'title' => (string) ($g['title'] ?? 'GIF') ?: 'GIF',
            'preview' => $preview,
            'width' => (int) ($g['images']['original']['width'] ?? 0) ?: null,
            'height' => (int) ($g['images']['original']['height'] ?? 0) ?: null,
        ];
    }

    private static function request(string $path, array $params): ?array
    {
        $url = rtrim((string) config('app.giphy.base'), '/') . $path . '?' . http_build_query(['api_key' => config('app.giphy.key')] + $params);
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 6, CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_FOLLOWLOCATION => false]);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if (!is_string($body) || $status !== 200) {
            return null;
        }
        $json = json_decode($body, true);
        return is_array($json) ? $json : null;
    }
}
