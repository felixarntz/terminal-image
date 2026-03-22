<?php
/**
 * Usage:
 *   php tools/cli.php <image-file>
 *
 * Example:
 *   php tools/cli.php unicorn.jpg
 */

require_once __DIR__ . '/../vendor/autoload.php';

use FelixArntz\TerminalImage\TerminalImage;

$file = $argv[1] ?? null;
if (!$file) {
    fwrite(STDERR, "Usage: php tools/cli.php <image-file>\n");
    exit(1);
}

echo TerminalImage::file($file);
