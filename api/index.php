<?php
/**
 * Vercel Serverless Function Handler
 * Routes all HTTP traffic to EveryVeda's Core PHP Front Controller
 */

declare(strict_types=1);

// Ensure working directory is set to root
chdir(dirname(__DIR__));

// Forward to public front controller
require __DIR__ . '/../public/index.php';
