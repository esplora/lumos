<?php

namespace Esplora\Lumos\Tests;

use Esplora\Lumos\Adapters\QpdfAdapter;
use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Providers\ArrayPasswordProvider;

class QpdfAdapterTest extends AdapterTests
{
    protected function adapter(): AdapterInterface
    {
        return new QpdfAdapter(
            $_SERVER['QPDF_BIN_PATH'] ?? 'qpdf'
        );
    }

    public function test_extraction_success(): void
    {
        $result = $this->adapter()
            ->extract(
                $this->getFixturesDir('pdf/simple.pdf'),
                $this->getExtractionPath(),
                $this->getPasswords()
            );

        $this->assertTrue($result->isSuccessful());
        $this->assertFilesExtracted([
            'simple.pdf',
        ]);
    }

    public function test_pdf_extraction_success_with_password(): void
    {
        $archivePath = $this->getFixturesDir('pdf/protected.pdf');

        $result = $this->adapter()
            ->extract($archivePath, $this->getExtractionPath(), $this->getPasswords());

        $this->assertTrue($result->isSuccessful());
        $this->assertFilesExtracted([
            'protected.pdf',
        ]);
    }

    public function test_extraction_failure_on_password(): void
    {
        $archivePath = $this->getFixturesDir('pdf/protected.pdf');

        $result = $this->adapter()->extract($archivePath, $this->getExtractionPath(), new ArrayPasswordProvider([
            'wrongpassword',
        ]));

        $this->assertFalse($result->isSuccessful());
    }
}
