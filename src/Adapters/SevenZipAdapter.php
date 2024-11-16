<?php

namespace Esplora\Lumos\Adapters;

use Esplora\Lumos\Concerns\DirectoryEnsurer;
use Esplora\Lumos\Concerns\HasExtractionSummary;
use Esplora\Lumos\Concerns\SupportsMimeTypes;
use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Contracts\ExtractionSummaryInterface;
use Esplora\Lumos\Contracts\PasswordProviderInterface;
use Symfony\Component\Process\Process;

/**
 * Handler for SevenZipArchive archive files.
 *
 * This class implements the ArchiveInterface and provides functionality for extracting .7z archives,
 * including support for passwords for protected archives.
 */
class SevenZipAdapter implements AdapterInterface
{
    use DirectoryEnsurer, SupportsMimeTypes, HasExtractionSummary;

    /**
     * @param string $bin
     */
    public function __construct(protected string $bin = '7z') {}

    /**
     * Returns the list of supported MIME types.
     *
     * @return array<string> Array of MIME types supported by this handler.
     */
    protected function supportedMimeTypes(): array
    {
        return [
            'application/x-7z-compressed',

            'application/gzip',
            'application/x-gzip',
            'application/vnd.rar',
            'application/x-rar-compressed',
            'application/x-tar',
            'application/zip',
        ];
    }

    /**
     * Extracts the contents of a 7-Zip archive to the specified directory.
     *
     * @param string                    $filePath    Path to the 7-Zip archive.
     * @param string                    $destination Directory where the archive will be extracted. The directory will be created if it does not exist.
     * @param PasswordProviderInterface $passwords   List of passwords for protected archives.
     */
    public function extract(string $filePath, string $destination, PasswordProviderInterface $passwords): ExtractionSummaryInterface
    {
        if ($this->tryExtract($filePath, $destination)) {
            return $this->summary(); // Successfully extracted without a password
        }

        // Attempt to extract the archive with each password from the list
        foreach ($passwords->getPasswords() as $password) {
            if ($this->tryExtract($filePath, $destination, $password)) {
                return $this->summary();
            }
        }

        return $this->summary();
    }

    /**
     * Attempts to extract the archive contents with an optional password.
     *
     * @param string      $filePath    Path to the 7-Zip archive.
     * @param string      $destination Directory for extracting the archive.
     * @param string|null $password    Password (optional).
     *
     * @return bool Returns true if extraction was successful, false otherwise.
     */
    protected function tryExtract(string $filePath, string $destination, ?string $password = null): bool
    {
        $this->ensureDirectoryExists($destination);

        $command = [
            $this->bin,
            'x',
            $filePath,
            '-o'.$destination,
            '-scsUTF-8',
            '-y',
            $password !== null ? '-p'.$password : '-p',
        ];

        $process = new Process($command);
        $process->run();

        $this->summary()->addStepForProcess($process, [
            'existPassword' => $password !== null,
        ]);

        return $process->isSuccessful();
    }

    /**
     * Checks if the required tools or libraries are installed for this adapter.
     *
     * @return bool Returns true if the environment is properly configured, false otherwise.
     */
    public function isSupportedEnvironment(): bool
    {
        $command = [$this->bin, 'i']; // i : Show information about supported formats

        $process = new Process($command);
        $process->run();

        return $process->isSuccessful();
    }
}
