<?php
/** app/Controllers/AttachmentController.php  -  serves shared files safely, and searches GIFs */
class AttachmentController extends Controller
{
    private const GIF_SEARCHES_PER_MINUTE = 30;

    public function show(int $id): void
    {
        $a = $this->orNotFound(Attachment::find($id));
        if ($a['stored_name'] === null || !Attachment::canView($a, Auth::user())) {
            Response::error(404);
        }
        $path = Attachment::path($a['stored_name']);
        if (!is_file($path)) {
            Response::error(404);
        }
        // Only verified images display in the page. Everything else always downloads, and the
        // browser is told not to guess the type or run anything inside the response.
        $isImage = $a['kind'] === 'image';
        $name = preg_replace('/[^\w.\- ]+/u', '_', $a['original_name']);
        header('Content-Type: ' . ($isImage ? $a['mime'] : 'application/octet-stream'));
        header('Content-Disposition: ' . ($isImage ? 'inline' : 'attachment') . '; filename="' . $name . '"');
        header('X-Content-Type-Options: nosniff');
        header("Content-Security-Policy: default-src 'none'; sandbox");
        header('Cache-Control: private, max-age=3600');
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }

    /** GIF picker. Goes through here so the API key never reaches the browser. */
    public function gifs(): void
    {
        if (!GiphyService::enabled()) {
            Response::json(['success' => false, 'message' => 'GIFs are not set up.', 'data' => (object) []], 503);
        }
        $window = Session::get('gif_window', ['at' => time(), 'count' => 0]);
        if (time() - $window['at'] >= 60) {
            $window = ['at' => time(), 'count' => 0];
        }
        $window['count']++;
        Session::set('gif_window', $window);
        if ($window['count'] > self::GIF_SEARCHES_PER_MINUTE) {
            Response::json(['success' => false, 'message' => 'Too many searches. Please wait a moment.', 'data' => (object) []], 429);
        }
        $gifs = GiphyService::search(mb_substr((string) Request::query('q'), 0, 50), max(0, (int) Request::query('offset')));
        if ($gifs === null) {
            Response::json(['success' => false, 'message' => "GIPHY can't be reached right now.", 'data' => (object) []], 502);
        }
        Response::json(['success' => true, 'message' => '', 'data' => ['gifs' => $gifs]]);
    }
}
