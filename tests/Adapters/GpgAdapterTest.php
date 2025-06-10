<?php

namespace Esplora\Lumos\Tests;

use Esplora\Lumos\Adapters\GpgAdapter;
use Esplora\Lumos\Adapters\QpdfAdapter;
use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Providers\ArrayPasswordProvider;

class GpgAdapterTest extends AdapterTests
{
    protected function adepter(): AdapterInterface
    {
        return new GpgAdapter(
            $_SERVER['GPG_BIN_PATH'] ?? 'gpg'
        );
    }

    public function test_extraction_success(): void
    {
        $result = $this->adepter()
            ->extract(
                $this->getFixturesDir('gpg/protected.txt.gpg'),
                $this->getExtractionPath(),
                $this->getPasswords()
            );

        $this->assertTrue($result->isSuccessful());
        $this->assertFilesExtracted([
            'protected.txt',
        ]);

        $this->assertStringEqualsFile(
            $this->getExtractionPath('protected.txt'),
            'Hello World!'
        );
    }

    public function test_extraction_failure_on_password(): void
    {
        $archivePath = $this->getFixturesDir('gpg/protected.txt.gpg');

        $result = $this->adepter()->extract($archivePath, $this->getExtractionPath(), new ArrayPasswordProvider([
            'wrongpassword',
        ]));

        $this->assertFalse($result->isSuccessful());
    }
}
