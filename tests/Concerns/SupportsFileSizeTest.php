<?php

namespace Esplora\Lumos\Tests\Concerns;

use Esplora\Lumos\Concerns\SupportsFileSize;
use Esplora\Lumos\Tests\DirectoryManagesTestData;
use PHPUnit\Framework\TestCase;

class SupportsFileSizeTest extends TestCase
{
    use DirectoryManagesTestData;

    public function test_file_size_below_limit(): void
    {
        $instance = new class
        {
            use SupportsFileSize;

            protected function getMaxFileSize(): int
            {
                return 1; // 1 byte
            }
        };

        $this->assertFalse($instance->canSupport($this->getFixturesDir('zip/protected.zip')));
    }

    public function test_file_size_within_limit(): void
    {
        $instance = new class
        {
            use SupportsFileSize;

            protected function getMaxFileSize(): int
            {
                return 1048576; // 1 MB
            }
        };

        $this->assertTrue($instance->canSupport($this->getFixturesDir('zip/protected.zip')));
    }
}
