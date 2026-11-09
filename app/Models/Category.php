<?php
/** app/Models/Category.php  -  Lecture Notes, Study Guides, Past Papers */
class Category
{
    public static function all(): array
    {
        return Database::all('SELECT * FROM categories ORDER BY id');
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::all() as $c) {
            $options[$c['id']] = $c['name'];
        }
        return $options;
    }

    public static function exists(int $id): bool
    {
        return Database::value('SELECT 1 FROM categories WHERE id = ?', [$id]) !== null;
    }
}
