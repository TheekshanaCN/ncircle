<?php
/** tests/giphy-mock.php  -  a pretend GIPHY for tests: php -S 127.0.0.1:8799 tests/giphy-mock.php */
header('Content-Type: application/json');
$gif = fn (string $id, string $rating) => [
    'id' => $id, 'title' => "Test {$id}", 'rating' => $rating,
    'images' => ['original' => ['width' => '200', 'height' => '150'], 'fixed_width_small' => ['url' => "https://media2.giphy.com/media/{$id}/100w.gif"]],
];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (($_GET['api_key'] ?? '') !== 'test-secret-key') {
    http_response_code(401);
    echo '{"message":"bad key"}';
} elseif (in_array($path, ['/v1/gifs/search', '/v1/gifs/trending'], true)) {
    echo json_encode(['data' => [$gif('SafeGif123', 'g'), $gif('SafeGif456', 'pg')]]);
} elseif ($path === '/v1/gifs/SafeGif123') {
    echo json_encode(['data' => $gif('SafeGif123', 'g')]);
} elseif ($path === '/v1/gifs/RatedRGif') {
    echo json_encode(['data' => $gif('RatedRGif', 'r')]);
} else {
    http_response_code(404);
    echo '{"message":"not found"}';
}
