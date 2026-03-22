<?php

/**
 * Generates test fixture images.
 *
 * Usage: php tests/fixtures/generate.php
 */

$dir = __DIR__;

$image = imagecreatetruecolor(2, 2);
$red = imagecolorallocate($image, 255, 0, 0);
$green = imagecolorallocate($image, 0, 255, 0);
$blue = imagecolorallocate($image, 0, 0, 255);
$white = imagecolorallocate($image, 255, 255, 255);

imagesetpixel($image, 0, 0, $red);
imagesetpixel($image, 1, 0, $green);
imagesetpixel($image, 0, 1, $blue);
imagesetpixel($image, 1, 1, $white);

imagepng($image, $dir . '/2x2.png');
imagedestroy($image);

echo "Generated 2x2.png\n";

$alphaImage = imagecreatetruecolor(2, 2);
imagealphablending($alphaImage, false);
imagesavealpha($alphaImage, true);

$transparent = imagecolorallocatealpha($alphaImage, 0, 0, 0, 127);
$opaqueRed = imagecolorallocatealpha($alphaImage, 255, 0, 0, 0);
$opaqueBlue = imagecolorallocatealpha($alphaImage, 0, 0, 255, 0);
$opaqueWhite = imagecolorallocatealpha($alphaImage, 255, 255, 255, 0);

imagesetpixel($alphaImage, 0, 0, $transparent);
imagesetpixel($alphaImage, 1, 0, $opaqueRed);
imagesetpixel($alphaImage, 0, 1, $opaqueBlue);
imagesetpixel($alphaImage, 1, 1, $opaqueWhite);

imagepng($alphaImage, $dir . '/2x2-alpha.png');
imagedestroy($alphaImage);

echo "Generated 2x2-alpha.png\n";
