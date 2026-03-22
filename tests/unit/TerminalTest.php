<?php

namespace FelixArntz\TerminalImage\Tests;

use FelixArntz\TerminalImage\Terminal;
use PHPUnit\Framework\TestCase;

class TerminalTest extends TestCase
{
    public function testGetColumnsReturnsPositiveInteger(): void
    {
        $columns = Terminal::getColumns();
        $this->assertIsInt($columns);
        $this->assertGreaterThan(0, $columns);
    }

    public function testGetRowsReturnsPositiveInteger(): void
    {
        $rows = Terminal::getRows();
        $this->assertIsInt($rows);
        $this->assertGreaterThan(0, $rows);
    }
}
