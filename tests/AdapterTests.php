<?php

namespace Esplora\Lumos\Tests;

use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Providers\ArrayPasswordProvider;
use PHPUnit\Framework\TestCase;

abstract class AdapterTests extends TestCase
{
    use DirectoryManagesTestData;

    abstract protected function adapter(): AdapterInterface;

    /**
     * Returns a list of passwords for testing.
     */
    protected function getPasswords(): ArrayPasswordProvider
    {
        return new ArrayPasswordProvider([
            '123',
            '12345',
            '123456',
            'Password1234_',
            'XcwfXuLNEY',
            'gg',
        ]);
    }

    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->safeLoad();

        parent::setUp();

        if (! $this->adapter()->isSupportedEnvironment()) {
            $this->markTestSkipped($this->adapter()::class.' is not supported.');
        }
    }

    protected function tearDown(): void
    {
        //  $this->deleteDir($this->getExtractionPath());
        parent::tearDown();
    }

    /**
     * Asserts that each file has been extracted.
     */
    protected function assertFilesExtracted(iterable $files = []): void
    {
        $files = empty($files) ? $this->getExpectedFiles() : $files;

        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->getExtractionPath($file)));
        }
    }

    protected function assertFilesExtractedAndEquivalent(iterable $files = []): void
    {
        $files = empty($files) ? $this->getExpectedFiles() : $files;

        foreach ($files as $file) {
            $filePath = $this->getExtractionPath($file);

            $fileReferencePath = $this->getReferenceDir($file);

            $this->assertEquals(
                hash_file('sha1', $fileReferencePath),
                hash_file('sha1', $filePath),
                "File $file is corrupted or has been modified"
            );
        }
    }

    /**
     * Asserts that each file has not been extracted.
     */
    protected function assertFilesDoesExtracted(): void
    {
        foreach ($this->getExpectedFiles() as $file) {
            $this->assertFileDoesNotExist($this->getExtractionPath($file));
        }
    }
}
