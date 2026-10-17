<?php
/**
 * EveryVeda Automated CLI Test Suite Runner
 * 
 * Usage:
 * php scripts/test_runner.php
 */

declare(strict_types=1);

ini_set('zend.assertions', '1');
ini_set('assert.active', '1');
ini_set('assert.exception', '1');

require_once __DIR__ . '/../app/bootstrap.php';

echo "\n";
echo "====================================================================\n";
echo "        EveryVeda.com Automated Test Suite Runner\n";
echo "        Core PHP 8.2+ & MariaDB Architectural Verification\n";
echo "====================================================================\n\n";

$startTime = microtime(true);
$testDirs = [
    ROOT_PATH . '/tests/Unit',
    ROOT_PATH . '/tests/Integration'
];

$testFiles = [];
foreach ($testDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*Test.php');
        if ($files) {
            $testFiles = array_merge($testFiles, $files);
        }
    }
}

if (empty($testFiles)) {
    echo "No test suites found in tests/Unit or tests/Integration.\n";
    exit(0);
}

$totalSuites = count($testFiles);
$totalTests = 0;
$totalPassed = 0;
$totalFailed = 0;
$failures = [];

foreach ($testFiles as $filePath) {
    require_once $filePath;

    $className = pathinfo($filePath, PATHINFO_FILENAME);
    // Find namespace from file content or default to Tests\Unit / Tests\Integration
    $isIntegration = str_contains($filePath, 'Integration');
    $fqcn = $isIntegration ? "Tests\\Integration\\{$className}" : "Tests\\Unit\\{$className}";

    if (!class_exists($fqcn)) {
        echo "[ERROR] Class {$fqcn} not found in {$filePath}\n";
        continue;
    }

    $reflector = new ReflectionClass($fqcn);
    $instance = $reflector->newInstance();

    echo "Suite: " . $reflector->getShortName() . "\n";
    echo str_repeat('-', 60) . "\n";

    $methods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
    foreach ($methods as $method) {
        $methodName = $method->getName();
        if (str_starts_with($methodName, 'test')) {
            $totalTests++;
            $testStart = microtime(true);

            try {
                $instance->$methodName();
                $testDuration = round((microtime(true) - $testStart) * 1000, 2);
                echo "  [PASS] {$methodName} ({$testDuration}ms)\n";
                $totalPassed++;
            } catch (\Throwable $e) {
                $testDuration = round((microtime(true) - $testStart) * 1000, 2);
                echo "  [FAIL] {$methodName} ({$testDuration}ms)\n";
                $totalFailed++;
                $failures[] = [
                    'suite' => $reflector->getShortName(),
                    'test' => $methodName,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ];
            }
        }
    }

    echo "\n";
}

$elapsed = round(microtime(true) - $startTime, 3);

echo "====================================================================\n";
echo " Test Execution Summary\n";
echo "====================================================================\n";
echo " Suites Executed : {$totalSuites}\n";
echo " Total Tests     : {$totalTests}\n";
echo " Passed          : {$totalPassed}\n";
echo " Failed          : {$totalFailed}\n";
echo " Time Elapsed    : {$elapsed} seconds\n";
echo "====================================================================\n";

if ($totalFailed > 0) {
    echo "\nFailures Detail:\n";
    foreach ($failures as $idx => $fail) {
        $n = $idx + 1;
        echo "{$n}) {$fail['suite']}::{$fail['test']}\n";
        echo "   Message: {$fail['message']}\n";
        echo "   Location: {$fail['file']}:{$fail['line']}\n\n";
    }
    exit(1);
} else {
    echo "\nAll automated tests passed successfully! [OK]\n\n";
    exit(0);
}
