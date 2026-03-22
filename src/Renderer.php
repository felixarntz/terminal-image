<?php

namespace FelixArntz\TerminalImage;

/**
 * Renders a GD image as ANSI escape sequences using Unicode half-block characters.
 *
 * @internal
 */
class Renderer
{
    private const PIXEL = "\u{2584}";
    private const ESC_RESET = "\033[0m";

    /**
     * Renders a GD image as a string of ANSI escape sequences.
     *
     * @param resource $image GD image resource to render.
     * @return string ANSI escape sequence string representing the image.
     */
    public static function render($image): string
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $lines = [];
        for ($y = 0; $y < $height - 1; $y += 2) {
            $line = '';
            for ($x = 0; $x < $width; $x++) {
                $topColor = self::getPixelColor($image, $x, $y);
                $bottomColor = self::getPixelColor($image, $x, $y + 1);

                $fg = sprintf("\033[38;2;%d;%d;%dm", $bottomColor['r'], $bottomColor['g'], $bottomColor['b']);

                if ($topColor['a'] === 127) {
                    $line .= $fg . self::PIXEL . self::ESC_RESET;
                } else {
                    $bg = sprintf("\033[48;2;%d;%d;%dm", $topColor['r'], $topColor['g'], $topColor['b']);
                    $line .= $bg . $fg . self::PIXEL . self::ESC_RESET;
                }
            }
            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    /**
     * Gets the RGBA color values for a pixel in a GD image.
     *
     * @param resource $image GD image resource to read from.
     * @param int      $x     Horizontal pixel coordinate.
     * @param int      $y     Vertical pixel coordinate.
     * @return array{r: int, g: int, b: int, a: int} Pixel color with red, green, blue, and alpha values.
     */
    private static function getPixelColor($image, int $x, int $y): array
    {
        $colorIndex = imagecolorat($image, $x, $y);
        if ($colorIndex === false) {
            return ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0];
        }

        $color = imagecolorsforindex($image, $colorIndex);

        return [
            'r' => $color['red'],
            'g' => $color['green'],
            'b' => $color['blue'],
            'a' => $color['alpha'],
        ];
    }
}
