<?php

namespace Esplora\Lumos\Tests\Concerns;

use Esplora\Lumos\Concerns\SupportsAlways;
use PHPUnit\Framework\TestCase;

class SupportsAlwaysTest extends TestCase
{
    public function test_always_returns_true(): void
    {
        $class = new class
        {
            use SupportsAlways;
        };

        $this->assertTrue($class->canSupport('/path/to/file.zip'));
        $this->assertTrue($class->canSupport('/path/to/anotherfile.rar'));
        $this->assertTrue($class->canSupport('/any/random/path.txt'));
    }
}
