<?php

namespace Esplora\Lumos\Tests;

use Esplora\Lumos\Adapters\GpgAdapter;
use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Providers\ArrayPasswordProvider;

class GpgAdapterTest extends AdapterTests
{
    protected function adapter(): AdapterInterface
    {
        return new GpgAdapter(
            $_SERVER['GPG_BIN_PATH'] ?? 'gpg'
        );
    }

    public function test_extraction_support(): void
    {
        $result = $this->adapter()->canSupport(
            $this->getFixturesDir('gpg/protected.txt.gpg')
        );

        $this->assertTrue($result);
    }

    public function test_extraction_success(): void
    {
        $result = $this->adapter()
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

        $result = $this->adapter()->extract($archivePath, $this->getExtractionPath(), new ArrayPasswordProvider([
            'wrongpassword',
        ]));

        $this->assertFalse($result->isSuccessful());
    }
}
