<?php

namespace FelixArntz\TerminalImage\Tests;

use FelixArntz\TerminalImage\Renderer;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    private const PIXEL = "\u{2584}";
    private const ESC_RESET = "\033[0m";

    public function testRenderProducesCorrectAnsiForTwoByTwoImage(): void
    {
        $image = imagecreatetruecolor(2, 2);
        $red = imagecolorallocate($image, 255, 0, 0);
        $green = imagecolorallocate($image, 0, 255, 0);
        $blue = imagecolorallocate($image, 0, 0, 255);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagesetpixel($image, 0, 0, $red);
        imagesetpixel($image, 1, 0, $green);
        imagesetpixel($image, 0, 1, $blue);
        imagesetpixel($image, 1, 1, $white);

        $result = Renderer::render($image);

        $expectedBgRed = "\033[48;2;255;0;0m";
        $expectedFgBlue = "\033[38;2;0;0;255m";
        $expectedBgGreen = "\033[48;2;0;255;0m";
        $expectedFgWhite = "\033[38;2;255;255;255m";

        $expectedPixel1 = $expectedBgRed . $expectedFgBlue . self::PIXEL . self::ESC_RESET;
        $expectedPixel2 = $expectedBgGreen . $expectedFgWhite . self::PIXEL . self::ESC_RESET;
        $expected = $expectedPixel1 . $expectedPixel2;

        $this->assertSame($expected, $result);

        @imagedestroy($image);
    }

    public function testRenderReturnsSingleLineForTwoRowImage(): void
    {
        $image = imagecreatetruecolor(1, 2);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagesetpixel($image, 0, 0, $black);
        imagesetpixel($image, 0, 1, $white);

        $result = Renderer::render($image);

        $this->assertStringNotContainsString("\n", $result);
        $this->assertStringContainsString(self::PIXEL, $result);

        @imagedestroy($image);
    }

    public function testRenderProducesMultipleLinesForFourRowImage(): void
    {
        $image = imagecreatetruecolor(1, 4);
        $color = imagecolorallocate($image, 128, 128, 128);

        for ($y = 0; $y < 4; $y++) {
            imagesetpixel($image, 0, $y, $color);
        }

        $result = Renderer::render($image);
        $lines = explode("\n", $result);

        $this->assertCount(2, $lines);

        @imagedestroy($image);
    }

    public function testRenderOmitsBackgroundForTransparentTopPixel(): void
    {
        $image = imagecreatetruecolor(2, 2);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        $opaqueRed = imagecolorallocatealpha($image, 255, 0, 0, 0);
        $opaqueBlue = imagecolorallocatealpha($image, 0, 0, 255, 0);
        $opaqueWhite = imagecolorallocatealpha($image, 255, 255, 255, 0);

        imagesetpixel($image, 0, 0, $transparent);
        imagesetpixel($image, 1, 0, $opaqueRed);
        imagesetpixel($image, 0, 1, $opaqueBlue);
        imagesetpixel($image, 1, 1, $opaqueWhite);

        $result = Renderer::render($image);

        $expectedPixel1 = "\033[38;2;0;0;255m" . self::PIXEL . self::ESC_RESET;
        $expectedPixel2 = "\033[48;2;255;0;0m" . "\033[38;2;255;255;255m" . self::PIXEL . self::ESC_RESET;
        $expected = $expectedPixel1 . $expectedPixel2;

        $this->assertSame($expected, $result);

        @imagedestroy($image);
    }

    public function testRenderIncludesBackgroundForOpaqueTopPixel(): void
    {
        $image = imagecreatetruecolor(1, 2);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $opaqueRed = imagecolorallocatealpha($image, 255, 0, 0, 0);
        $opaqueBlue = imagecolorallocatealpha($image, 0, 0, 255, 0);

        imagesetpixel($image, 0, 0, $opaqueRed);
        imagesetpixel($image, 0, 1, $opaqueBlue);

        $result = Renderer::render($image);

        $expected = "\033[48;2;255;0;0m" . "\033[38;2;0;0;255m" . self::PIXEL . self::ESC_RESET;

        $this->assertSame($expected, $result);

        @imagedestroy($image);
    }
}
