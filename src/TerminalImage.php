<?php

namespace FelixArntz\TerminalImage;

use InvalidArgumentException;
use RuntimeException;

/**
 * Public API for rendering images as ANSI escape sequences for terminal display.
 */
class TerminalImage
{
    private const ROW_OFFSET = 2;

    /**
     * Renders an image from a file path as a string of ANSI escape sequences.
     *
     * @param string               $filePath Absolute or relative path to the image file.
     * @param array<string, mixed> $options  Optional. Additional options. Default empty array.
     * @return string ANSI escape sequence string representing the image.
     *
     * @throws InvalidArgumentException If the file does not exist, cannot be read, or contains invalid image data.
     * @throws RuntimeException If the image cannot be resized.
     */
    public static function file(string $filePath, array $options = []): string
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException(sprintf('File does not exist: %s', $filePath));
        }

        $contents = @file_get_contents($filePath);
        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Unable to read file: %s', $filePath));
        }

        return self::renderFromData($contents, $options);
    }

    /**
     * Renders an image from a binary string as a string of ANSI escape sequences.
     *
     * @param string               $data    Binary string of image data.
     * @param array<string, mixed> $options Optional. Additional options. Default empty array.
     * @return string ANSI escape sequence string representing the image.
     *
     * @throws InvalidArgumentException If the data is not a valid image.
     * @throws RuntimeException If the image cannot be resized.
     */
    public static function buffer(string $data, array $options = []): string
    {
        return self::renderFromData($data, $options);
    }

    /**
     * Renders an image from raw data as a string of ANSI escape sequences.
     *
     * @param string               $data    Binary string of image data.
     * @param array<string, mixed> $options Additional options.
     * @return string ANSI escape sequence string representing the image.
     *
     * @throws InvalidArgumentException If the data is not a valid image.
     * @throws RuntimeException If the image cannot be resized.
     */
    private static function renderFromData(string $data, array $options): string
    {
        $image = @imagecreatefromstring($data);
        if ($image === false) {
            throw new InvalidArgumentException('Invalid or unsupported image data.');
        }

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $dimensions = self::calculateDimensions($imageWidth, $imageHeight, $options);

        $resized = imagescale($image, $dimensions['width'], $dimensions['height']);
        if ($resized === false) {
            @imagedestroy($image);
            throw new RuntimeException('Unable to resize image.');
        }

        @imagedestroy($image);

        $result = Renderer::render($resized);

        @imagedestroy($resized);

        return $result;
    }

    /**
     * Calculates the target dimensions for rendering an image.
     *
     * @param int                  $imageWidth  Original image width in pixels.
     * @param int                  $imageHeight Original image height in pixels.
     * @param array<string, mixed> $options     Additional options.
     * @return array{width: int, height: int} Target width and height in pixels.
     *
     * @throws InvalidArgumentException If an option value is invalid.
     */
    private static function calculateDimensions(int $imageWidth, int $imageHeight, array $options): array
    {
        $terminalColumns = Terminal::getColumns();
        $terminalRows = max(1, Terminal::getRows() - self::ROW_OFFSET);

        $inputWidth = isset($options['width']) ? $options['width'] : null;
        $inputHeight = isset($options['height']) ? $options['height'] : null;
        $preserveAspectRatio = isset($options['preserveAspectRatio']) ? (bool) $options['preserveAspectRatio'] : true;

        if ($inputHeight !== null && $inputWidth !== null) {
            $width = (float) self::checkAndGetDimensionValue($inputWidth, $terminalColumns);
            $height = (float) self::checkAndGetDimensionValue($inputHeight, $terminalRows) * 2;
            if ($preserveAspectRatio) {
                $scaled = self::scale($width, $height, (float) $imageWidth, (float) $imageHeight);
                $width = $scaled['width'];
                $height = $scaled['height'];
            }
        } elseif ($inputWidth !== null) {
            $width = (float) self::checkAndGetDimensionValue($inputWidth, $terminalColumns);
            $height = $imageHeight * $width / $imageWidth;
        } elseif ($inputHeight !== null) {
            $height = (float) self::checkAndGetDimensionValue($inputHeight, $terminalRows) * 2;
            $width = $imageWidth * $height / $imageHeight;
        } else {
            $scaled = self::scale(
                (float) $terminalColumns,
                (float) $terminalRows * 2,
                (float) $imageWidth,
                (float) $imageHeight
            );
            $width = $scaled['width'];
            $height = $scaled['height'];
        }

        if ($width > $terminalColumns) {
            $scaled = self::scale((float) $terminalColumns, (float) $terminalRows * 2, $width, $height);
            $width = $scaled['width'];
            $height = $scaled['height'];
        }

        return [
            'width' => (int) round($width),
            'height' => (int) round($height),
        ];
    }

    /**
     * Validates and resolves a dimension value to an absolute integer.
     *
     * @param mixed $value          Dimension value as percentage string or absolute integer.
     * @param int   $percentageBase Base value to resolve percentages against.
     * @return int Resolved absolute dimension value.
     *
     * @throws InvalidArgumentException If the value is not a valid dimension.
     */
    private static function checkAndGetDimensionValue($value, int $percentageBase): int
    {
        if (is_string($value) && substr($value, -1) === '%') {
            $percentageValue = (float) $value;
            if (!is_nan($percentageValue) && $percentageValue > 0 && $percentageValue <= 100) {
                return (int) floor($percentageValue / 100 * $percentageBase);
            }
        }

        if (is_int($value)) {
            if ($value <= 0) {
                throw new InvalidArgumentException(
                    sprintf(
                        '%s is not a valid dimension value',
                        (string) $value
                    )
                );
            }
            return $value;
        }

        throw new InvalidArgumentException(
            sprintf(
                '%s is not a valid dimension value',
                is_scalar($value) ? (string) $value : gettype($value)
            )
        );
    }

    /**
     * Scales dimensions to fit within a bounding box while preserving aspect ratio.
     *
     * @param float $width          Maximum width of the bounding box.
     * @param float $height         Maximum height of the bounding box.
     * @param float $originalWidth  Original width to scale.
     * @param float $originalHeight Original height to scale.
     * @return array{width: float, height: float} Scaled width and height.
     */
    private static function scale(
        float $width,
        float $height,
        float $originalWidth,
        float $originalHeight
    ): array {
        $originalRatio = $originalWidth / $originalHeight;
        if ($width / $height > $originalRatio) {
            $factor = $height / $originalHeight;
        } else {
            $factor = $width / $originalWidth;
        }

        return [
            'width' => $factor * $originalWidth,
            'height' => $factor * $originalHeight,
        ];
    }
}
