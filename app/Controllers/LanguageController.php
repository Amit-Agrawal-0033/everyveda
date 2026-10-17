<?php
/**
 * Language Switching Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\TranslationService;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale): void
    {
        TranslationService::setLocale($locale);

        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        $this->redirect($referer);
    }
}
