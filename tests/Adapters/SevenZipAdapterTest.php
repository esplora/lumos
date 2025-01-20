<?php

namespace Esplora\Lumos\Tests;

use Esplora\Lumos\Adapters\SevenZipAdapter;
use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Providers\ArrayPasswordProvider;

class SevenZipAdapterTest extends AdapterTests
{
    protected function adepter(): AdapterInterface
    {
        return new SevenZipAdapter(
            $_SERVER['SEVEN_ZIP_BIN_PATH'] ?? '7z'
        );
    }

    public function test_extraction_success(): void
    {
        $result = $this->adepter()
            ->extract(
                $this->getFixturesDir('zip/simple.zip'),
                $this->getExtractionPath(),
                $this->getPasswords()
            );

        $this->assertNull($result->password());
        $this->assertTrue($result->isSuccessful());
        $this->assertFilesExtracted([
            'simple/simple.txt',
        ]);
    }

    public function test_extraction_success_with_password(): void
    {
        $archivePath = $this->getFixturesDir('zip/protected.zip');

        $result = $this->adepter()
            ->extract(
                $archivePath,
                $this->getExtractionPath(),
                $this->getPasswords()
            );

        $this->assertSame('12345', $result->password());
        $this->assertTrue($result->isSuccessful());
        $this->assertFilesExtracted([
            'protected/simple.txt',
        ]);
    }

    public function test_extraction_multipart_with_password(): void
    {
        $archivePath = $this->getFixturesDir('zip/gg.zip.001');

        $result = $this->adepter()
            ->extract(
                $archivePath,
                $this->getExtractionPath(),
                $this->getPasswords()
            );

        $this->assertFalse($result->isSuccessful());
    }

    public function test_extraction_multipart_with_password2(): void
    {
        $archivePath = $this->getFixturesDir('zip/XcwfXuLNEY.zip');

        $result = $this->adepter()
            ->extract(
                $archivePath,
                $this->getExtractionPath(),
                $this->getPasswords()
            );

        $this->assertFalse($result->isSuccessful());
    }

    public function test_extraction_failure_on_password(): void
    {
        $passwords = [
            'wrongpassword',
            'fewfwefgwegreg',
            'gregre0u089ujg',
        ];

        $archivePath = $this->getFixturesDir('zip/protected.zip');

        $result = $this->adepter()
            ->extract(
                $archivePath,
                $this->getExtractionPath(),
                new ArrayPasswordProvider($passwords)
            );

        $this->assertFalse($result->isSuccessful());

        // Increment to one more than the number of passwords because the first attempt is without a password
        $this->assertEquals(count($passwords) + 1, $result->attempts());

        // Only unique output
        $this->assertEquals(1, $result->steps()->count());

        // $this->assertFilesDoesExtracted();
    }
}
