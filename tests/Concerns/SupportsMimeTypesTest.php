<?php

namespace Esplora\Lumos\Tests\Concerns;

use Esplora\Lumos\Concerns\SupportsMimeTypes;
use Esplora\Lumos\Tests\DirectoryManagesTestData;
use PHPUnit\Framework\TestCase;

class SupportsMimeTypesTest extends TestCase
{
    use DirectoryManagesTestData;

    public function test_can_support_with_supported_mime_type(): void
    {
        $instance = new class
        {
            use SupportsMimeTypes;

            protected function supportedMimeTypes(): array
            {
                return ['application/zip', 'application/x-tar'];
            }
        };

        $this->assertTrue($instance->canSupport($this->getFixturesDir('zip/simple.zip')));
        $this->assertTrue($instance->canSupport($this->getFixturesDir('zip/protected.zip')));
    }

    public function test_can_support_with_unsupported_mime_type(): void
    {
        $instance = new class
        {
            use SupportsMimeTypes;

            protected function supportedMimeTypes(): array
            {
                return ['application/x-blorb'];
            }
        };

        $this->assertFalse($instance->canSupport($this->getFixturesDir('zip/protected.zip')));
    }

    public function test_can_support_with_empty_supported_mime_types(): void
    {
        $instance = new class
        {
            use SupportsMimeTypes;

            protected function supportedMimeTypes(): array
            {
                return [];
            }
        };

        $this->assertFalse($instance->canSupport($this->getFixturesDir('zip/protected.zip')));
    }
}
