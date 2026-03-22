<?php

namespace FelixArntz\TerminalImage;

/**
 * Detects terminal dimensions via tput, with fallback defaults.
 *
 * @internal
 */
class Terminal
{
    private const DEFAULT_COLUMNS = 80;
    private const DEFAULT_ROWS = 24;

    /**
     * Gets the number of terminal columns.
     *
     * @return int Number of terminal columns.
     */
    public static function getColumns(): int
    {
        return self::getTputValue('cols', self::DEFAULT_COLUMNS);
    }

    /**
     * Gets the number of terminal rows.
     *
     * @return int Number of terminal rows.
     */
    public static function getRows(): int
    {
        return self::getTputValue('lines', self::DEFAULT_ROWS);
    }

    /**
     * Gets a terminal dimension value via tput.
     *
     * @param string $capability The tput capability name to query.
     * @param int    $fallback   Fallback value if tput fails.
     * @return int Resolved terminal dimension value.
     */
    private static function getTputValue(string $capability, int $fallback): int
    {
        $output = [];
        $resultCode = 0;
        @exec('tput ' . $capability . ' 2>/dev/null', $output, $resultCode);

        if ($resultCode !== 0 || empty($output)) {
            return $fallback;
        }

        $value = (int) $output[0];
        return $value > 0 ? $value : $fallback;
    }
}
