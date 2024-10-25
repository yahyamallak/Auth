<?php

namespace Yahya\Auth;

class Installer
{
    public static function postInstall($event)
    {
        self::copyMigrations($event);
    }

    public static function postUpdate($event)
    {
        self::copyMigrations($event);
    }

    private static function copyMigrations($event)
    {
        $source = __DIR__ . '/../migrations';
        $target = getcwd() . '/migrations';

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        foreach (scandir($source) as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            copy("$source/$file", "$target/$file");
        }

        $event->getIO()->write("Migrations have been copied to the migrations folder.");
    }
}
