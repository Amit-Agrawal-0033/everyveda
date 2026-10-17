<?php
/**
 * Translation and Localization Service
 */

declare(strict_types=1);

namespace App\Services;

class TranslationService
{
    private static array $supportedLocales = ['en', 'hi'];

    public static function getLocale(): string
    {
        return current_locale();
    }

    public static function setLocale(string $locale): void
    {
        if (in_array($locale, self::$supportedLocales, true)) {
            $_SESSION['locale'] = $locale;
            setcookie('everyveda_locale', $locale, [
                'expires' => time() + 86400 * 365,
                'path' => '/',
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
        }
    }

    public static function getSupportedLocales(): array
    {
        return [
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native' => 'English'
            ],
            'hi' => [
                'code' => 'hi',
                'name' => 'Hindi',
                'native' => 'हिन्दी'
            ]
        ];
    }
}
