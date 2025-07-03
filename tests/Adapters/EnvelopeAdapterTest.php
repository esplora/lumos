<?php

namespace Esplora\Lumos\Tests;

use Esplora\Lumos\Adapters\EnvelopeAdapter;
use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Providers\ArrayPasswordProvider;

class EnvelopeAdapterTest extends AdapterTests
{
    protected function adapter(): AdapterInterface
    {
        return new EnvelopeAdapter;
    }

    public function test_extraction_success(): void
    {
        $result = $this->adapter()
            ->extract(
                $this->getFixturesDir('eml/simple.eml'),
                $this->getExtractionPath(),
                new ArrayPasswordProvider([])
            );

        $this->assertTrue($result->isSuccessful());
        $this->assertFilesExtracted([
            'simple.txt',
        ]);
    }
}
