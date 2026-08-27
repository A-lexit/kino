<?php
namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';
    case User = 'user'; // звичайний відвідувач сайту (коментарі тощо) — БЕЗ доступу в адмінку

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Адміністратор',
            self::Editor => 'Редактор',
            self::Viewer => 'Переглядач',
            self::User => 'Користувач сайту',
        };
    }

    /**
     * Ролі, яким дозволено заходити в адмінку взагалі (розділ "Фільми").
     */
    public static function staffRoles(): array
    {
        return [self::Admin, self::Editor, self::Viewer];
    }

}
