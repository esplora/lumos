<?php

namespace Esplora\Lumos\Tests\Concerns;

use Esplora\Lumos\Concerns\SupportsFileExtensions;
use PHPUnit\Framework\TestCase;

class SupportsFileExtensionsTest extends TestCase
{
    public function test_is_file_extension_allowed(): void
    {
        $instance = new class
        {
            use SupportsFileExtensions;

            protected function allowedExtensions(): array
            {
                return ['txt', 'zip'];
            }
        };

        $this->assertTrue($instance->canSupport('file.zip'));
        $this->assertFalse($instance->canSupport('image.jpg'));
    }
}
