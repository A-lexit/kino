<?php
declare(strict_types=1);

// На Vercel (serverless) файлова система тільки для читання, окрім /tmp.
// Перенаправляємо всі Laravel storage/cache шляхи туди ПЕРЕД завантаженням застосунку.
$tmpBase = '/tmp/storage';

foreach (['framework/views', 'framework/cache/data', 'framework/sessions', 'logs', 'app/public'] as $dir) {
    $path = $tmpBase . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

putenv('VIEW_COMPILED_PATH=' . $tmpBase . '/framework/views');

require __DIR__ . '/../public/index.php';
