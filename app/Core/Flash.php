<?php
/**
 * app/Core/Flash.php  -  one-time messages shown by views/partials/flash.php
 *   Flash::add('success', 'Uploaded.');
 */
class Flash
{
    public static function add(string $type, string $message): void
    {
        $messages = $_SESSION['_flash']['messages'] ?? [];
        $messages[] = ['type' => $type, 'message' => $message];
        Session::flash('messages', $messages);
    }

    public static function all(): array
    {
        return Session::peek('messages', []);
    }
}
