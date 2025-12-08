<?php

namespace App\Utils;

class EnvLoader
{
    public static function load($path = null)
    {
        if ($path === null) {
            $path = __DIR__ . '/../../.env';
        }

        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $value = trim($value, '"\''); 
                
                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }
}

