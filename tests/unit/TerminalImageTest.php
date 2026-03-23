<?php

namespace FelixArntz\TerminalImage\Tests;

use FelixArntz\TerminalImage\TerminalImage;
use PHPUnit\Framework\TestCase;

class TerminalImageTest extends TestCase
{
    private const PIXEL = "\u{2584}";
    private const FIXTURE_PATH = __DIR__ . '/../fixtures/2x2.png';
    private const FIXTURE_PATH_JPG = __DIR__ . '/../fixtures/2x2.jpg';
    private const FIXTURE_PATH_WEBP = __DIR__ . '/../fixtures/2x2.webp';

    public function testFileReturnsAnsiStringForPngFixture(): void
    {
        $result = TerminalImage::file(self::FIXTURE_PATH);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
        $this->assertStringContainsString("\033[48;2;", $result);
        $this->assertStringContainsString("\033[38;2;", $result);
        $this->assertStringContainsString("\033[0m", $result);
    }

    public function testFileReturnsAnsiStringForJpgFixture(): void
    {
        $result = TerminalImage::file(self::FIXTURE_PATH_JPG);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
        $this->assertStringContainsString("\033[48;2;", $result);
        $this->assertStringContainsString("\033[38;2;", $result);
        $this->assertStringContainsString("\033[0m", $result);
    }

    public function testFileReturnsAnsiStringForWebpFixture(): void
    {
        $result = TerminalImage::file(self::FIXTURE_PATH_WEBP);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
        $this->assertStringContainsString("\033[48;2;", $result);
        $this->assertStringContainsString("\033[38;2;", $result);
        $this->assertStringContainsString("\033[0m", $result);
    }

    public function testFileThrowsForNonexistentFile(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File does not exist: /nonexistent/image.png');
        TerminalImage::file('/nonexistent/image.png');
    }

    public function testFileThrowsForInvalidImageData(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'terminal_image_test_');
        file_put_contents($tmpFile, 'not an image');

        try {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage('Invalid or unsupported image data.');
            TerminalImage::file($tmpFile);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testBufferReturnsAnsiStringForPngData(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);
        $result = TerminalImage::buffer($data);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
        $this->assertStringContainsString("\033[48;2;", $result);
        $this->assertStringContainsString("\033[38;2;", $result);
        $this->assertStringContainsString("\033[0m", $result);
    }

    public function testBufferReturnsAnsiStringForJpgData(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH_JPG);
        $result = TerminalImage::buffer($data);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
        $this->assertStringContainsString("\033[48;2;", $result);
        $this->assertStringContainsString("\033[38;2;", $result);
        $this->assertStringContainsString("\033[0m", $result);
    }

    public function testBufferReturnsAnsiStringForWebpData(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH_WEBP);
        $result = TerminalImage::buffer($data);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
        $this->assertStringContainsString("\033[48;2;", $result);
        $this->assertStringContainsString("\033[38;2;", $result);
        $this->assertStringContainsString("\033[0m", $result);
    }

    public function testBufferOutputMatchesFileOutput(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);
        $bufferResult = TerminalImage::buffer($data);
        $fileResult = TerminalImage::file(self::FIXTURE_PATH);

        $this->assertSame($fileResult, $bufferResult);
    }

    public function testBufferThrowsForInvalidData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid or unsupported image data.');
        TerminalImage::buffer('not an image');
    }

    public function testBufferWithWidthOption(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);
        $result = TerminalImage::buffer($data, ['width' => 40]);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
    }

    public function testBufferWithHeightOption(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);
        $result = TerminalImage::buffer($data, ['height' => 10]);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
    }

    public function testBufferWithBothDimensions(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);
        $result = TerminalImage::buffer($data, ['width' => 20, 'height' => 10]);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
    }

    public function testBufferWithPercentageWidth(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);
        $result = TerminalImage::buffer($data, ['width' => '50%']);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
    }

    public function testBufferWithPreserveAspectRatioFalse(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);
        $result = TerminalImage::buffer($data, [
            'width' => 20,
            'height' => 10,
            'preserveAspectRatio' => false,
        ]);

        $this->assertIsString($result);
        $this->assertStringContainsString(self::PIXEL, $result);
    }

    public function testCheckAndGetDimensionValueWithAbsoluteInt(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->assertSame(40, $method->invoke(null, 40, 80));
    }

    public function testCheckAndGetDimensionValueWithPercentage(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->assertSame(40, $method->invoke(null, '50%', 80));
    }

    public function testCheckAndGetDimensionValueWithPercentageRoundsDown(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->assertSame(26, $method->invoke(null, '33%', 80));
    }

    public function testCheckAndGetDimensionValueWith100Percent(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->assertSame(80, $method->invoke(null, '100%', 80));
    }

    public function testCheckAndGetDimensionValueThrowsForInvalidString(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->expectException(\InvalidArgumentException::class);
        $method->invoke(null, 'invalid', 80);
    }

    public function testCheckAndGetDimensionValueThrowsForZeroPercent(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->expectException(\InvalidArgumentException::class);
        $method->invoke(null, '0%', 80);
    }

    public function testCheckAndGetDimensionValueThrowsForNegativePercent(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->expectException(\InvalidArgumentException::class);
        $method->invoke(null, '-10%', 80);
    }

    public function testCheckAndGetDimensionValueThrowsForOver100Percent(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->expectException(\InvalidArgumentException::class);
        $method->invoke(null, '110%', 80);
    }

    public function testCheckAndGetDimensionValueThrowsForFloat(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->expectException(\InvalidArgumentException::class);
        $method->invoke(null, 40.5, 80);
    }

    public function testCalculateDimensionsDefaultNoOptions(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'calculateDimensions');

        $result = $method->invoke(null, 200, 100, []);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertIsInt($result['width']);
        $this->assertIsInt($result['height']);
        $this->assertGreaterThan(0, $result['width']);
        $this->assertGreaterThan(0, $result['height']);
    }

    public function testCalculateDimensionsWithWidthOnly(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'calculateDimensions');

        $result = $method->invoke(null, 200, 100, ['width' => 40]);

        $this->assertSame(40, $result['width']);
        $this->assertSame(20, $result['height']);
    }

    public function testCalculateDimensionsWithHeightOnly(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'calculateDimensions');

        $result = $method->invoke(null, 200, 100, ['height' => 5]);

        $this->assertSame(20, $result['width']);
        $this->assertSame(10, $result['height']);
    }

    public function testCalculateDimensionsWithBothPreserveAspectRatio(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'calculateDimensions');

        $result = $method->invoke(null, 200, 100, ['width' => 40, 'height' => 10]);

        $this->assertLessThanOrEqual(40, $result['width']);
        $this->assertLessThanOrEqual(20, $result['height']);
        $this->assertGreaterThan(0, $result['width']);
        $this->assertGreaterThan(0, $result['height']);
    }

    public function testCalculateDimensionsWithBothNoPreserveAspectRatio(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'calculateDimensions');

        $result = $method->invoke(null, 200, 100, [
            'width' => 40,
            'height' => 10,
            'preserveAspectRatio' => false,
        ]);

        $this->assertSame(40, $result['width']);
        $this->assertSame(20, $result['height']);
    }

    public function testCalculateDimensionsClampToTerminalWidth(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'calculateDimensions');

        $terminalColumns = \FelixArntz\TerminalImage\Terminal::getColumns();

        $result = $method->invoke(null, 200, 100, ['width' => $terminalColumns + 100]);

        $this->assertLessThanOrEqual($terminalColumns, $result['width']);
    }

    public function testCalculateDimensionsWithPercentageWidth(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'calculateDimensions');

        $terminalColumns = \FelixArntz\TerminalImage\Terminal::getColumns();
        $expectedWidth = (int) floor($terminalColumns * 0.5);

        $result = $method->invoke(null, 200, 100, ['width' => '50%']);

        $this->assertSame($expectedWidth, $result['width']);
    }

    public function testCalculateDimensionsWithPercentageHeight(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'calculateDimensions');

        $result = $method->invoke(null, 200, 100, ['height' => '50%']);

        $this->assertIsInt($result['width']);
        $this->assertIsInt($result['height']);
        $this->assertGreaterThan(0, $result['width']);
        $this->assertGreaterThan(0, $result['height']);
    }

    public function testCheckAndGetDimensionValueThrowsForNegativeInt(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('-5 is not a valid dimension value');
        $method->invoke(null, -5, 80);
    }

    public function testCheckAndGetDimensionValueThrowsForZeroInt(): void
    {
        $method = self::makeAccessibleReflectionMethod(TerminalImage::class, 'checkAndGetDimensionValue');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('0 is not a valid dimension value');
        $method->invoke(null, 0, 80);
    }

    public function testBufferThrowsForNegativeWidth(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);

        $this->expectException(\InvalidArgumentException::class);
        TerminalImage::buffer($data, ['width' => -5]);
    }

    public function testBufferThrowsForNegativeHeight(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);

        $this->expectException(\InvalidArgumentException::class);
        TerminalImage::buffer($data, ['height' => -5]);
    }

    public function testBufferThrowsForZeroWidth(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);

        $this->expectException(\InvalidArgumentException::class);
        TerminalImage::buffer($data, ['width' => 0]);
    }

    public function testBufferThrowsForZeroHeight(): void
    {
        $data = file_get_contents(self::FIXTURE_PATH);

        $this->expectException(\InvalidArgumentException::class);
        TerminalImage::buffer($data, ['height' => 0]);
    }

    private static function makeAccessibleReflectionMethod(string $class, string $methodName): \ReflectionMethod
    {
        $method = new \ReflectionMethod($class, $methodName);

        // In PHP 8.1 and later, `setAccessible` is no longer needed.
        // In PHP 8.5, it is deprecated.
        if (version_compare(PHP_VERSION, '8.1', '<')) {
            $method->setAccessible(true);
        }
        return $method;
    }
}
