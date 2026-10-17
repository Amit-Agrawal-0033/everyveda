<?php
/**
 * EveryVeda View Engine
 * Template rendering with layout inheritance and global context injection
 */

declare(strict_types=1);

namespace App\Core;

use App\Services\CartService;
use App\Models\Category;
use App\Models\Setting;

class View
{
    private static ?array $cachedSettings = null;
    private static ?array $cachedNavCategories = null;

    /**
     * Render a view file within an optional layout
     */
    public static function render(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): void
    {
        $content = self::renderPartial($viewPath, $data);

        if ($layout) {
            $layoutFile = APP_PATH . "/Views/{$layout}.php";
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout file {$layoutFile} not found.");
            }

            // Extract data for layout
            $layoutData = array_merge(self::getGlobals(), $data, ['content' => $content]);
            extract($layoutData, EXTR_SKIP);

            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Render partial template without layout
     */
    public static function renderPartial(string $viewPath, array $data = []): string
    {
        $viewFile = APP_PATH . "/Views/{$viewPath}.php";
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file {$viewFile} not found.");
        }

        $mergedData = array_merge(self::getGlobals(), $data);
        extract($mergedData, EXTR_SKIP);

        ob_start();
        require $viewFile;
        return ob_get_clean() ?: '';
    }

    /**
     * Common global variables shared across all views
     */
    public static function getGlobals(): array
    {
        if (self::$cachedSettings === null) {
            try {
                $rows = Database::fetchAll("SELECT `key`, `value` FROM `settings`");
                self::$cachedSettings = [];
                foreach ($rows as $r) {
                    self::$cachedSettings[$r['key']] = $r['value'];
                }
            } catch (\Throwable $e) {
                self::$cachedSettings = [];
            }
        }

        if (self::$cachedNavCategories === null) {
            try {
                self::$cachedNavCategories = Database::fetchAll(
                    "SELECT `id`, `slug`, `name_en`, `name_hi`, `image` 
                     FROM `categories` 
                     WHERE `is_active` = 1 AND `parent_id` IS NULL 
                     ORDER BY `sort_order` ASC, `id` ASC"
                );
            } catch (\Throwable $e) {
                self::$cachedNavCategories = [];
            }
        }

        $cartCount = 0;
        try {
            $cartCount = CartService::getItemCount();
        } catch (\Throwable $e) {
            $cartCount = 0;
        }

        return [
            'settings' => self::$cachedSettings,
            'navCategories' => self::$cachedNavCategories,
            'cartCount' => $cartCount,
            'user' => auth_user(),
            'admin' => admin_user(),
            'currentLocale' => current_locale(),
            'flashes' => get_flashes()
        ];
    }
}
