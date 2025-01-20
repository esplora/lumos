<?php

namespace Esplora\Lumos\Contracts;

interface AdapterInterface
{
    /**
     * Checks if the handler can support the given archive format.
     *
     * The method should return true if the archive format is supported, and false otherwise.
     *
     * @param string $filePath Path to the archive to check.
     *
     * @return bool Returns true if the archive format is supported, false otherwise.
     */
    public function canSupport(string $filePath): bool;

    /**
     * Checks if the required tools or libraries are installed for this adapter.
     *
     * @return bool Returns true if the environment is properly configured, false otherwise.
     */
    public function isSupportedEnvironment(): bool;

    /**
     * Extracts the contents of the archive to the specified location.
     *
     * The method should extract the archive located at $filePath to the $destination directory.
     * If the archive is password-protected, the method should use the provided passwords from $passwords to attempt
     * extraction. If the archive contains multiple files or directories, they should be extracted to the specified
     * location.
     *
     * @param string                    $filePath    Path to the archive to extract.
     * @param string                    $destination Directory where the archive will be extracted. The directory will be created if it does not exist.
     * @param PasswordProviderInterface $passwords   Password provider object to attempt extraction if the archive is password-protected.
     *                                               Can be an array or any iterable object containing password strings.
     *
     * @return SummaryInterface Returns the summary of the extraction process.
     */
    public function extract(string $filePath, string $destination, PasswordProviderInterface $passwords): SummaryInterface;
}
