<?php
/**
 * Banner & Homepage Sliders Data Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Banner
{
    /**
     * Get active banners for homepage
     */
    public static function getActive(?string $locale = null): array
    {
        $locale = $locale ?: current_locale();
        $titleField = $locale === 'hi' ? 'title_hi' : 'title_en';
        $subField = $locale === 'hi' ? 'subtitle_hi' : 'subtitle_en';

        $banners = Database::fetchAll(
            "SELECT id, image_path, link_url, {$titleField} AS title, {$subField} AS subtitle, title_en, title_hi
             FROM banners 
             WHERE is_active = 1 
             ORDER BY sort_order ASC, id ASC"
        );

        return $banners;
    }
}
