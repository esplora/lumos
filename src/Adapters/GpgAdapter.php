<?php

namespace Esplora\Lumos\Adapters;

use Esplora\Lumos\Concerns\Decryptable;
use Esplora\Lumos\Concerns\SupportsFileExtensions;
use Esplora\Lumos\Contracts\AdapterInterface;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class GpgAdapter implements AdapterInterface
{
    use Decryptable, SupportsFileExtensions;

    public function __construct(protected string $bin = 'gpg') {}

    /**
     * Returns the list of allowed file extensions.
     *
     * @return array<string> Array of allowed file extensions.
     */
    public function allowedExtensions(): array
    {
        return ['.gpg'];
    }

    /**
     * Attempts to decrypt the GPG-encrypted file using the provided password.
     *
     * @param string      $filePath    Path to the encrypted file.
     * @param string      $destination Output directory for decrypted file.
     * @param string|null $password    Password used for decryption.
     *
     * @return bool
     */
    protected function tryDecrypting(string $filePath, string $destination, ?string $password = null): bool
    {
        if ($password === null) {
            return false;
        }

        $outputFile = Str::of($destination)
            ->finish('/')
            ->append(pathinfo($filePath, PATHINFO_FILENAME))
            ->toString();

        $command = [
            $this->bin,
            '--batch',
            '--yes',
            '--passphrase', $password,
            '--decrypt',
            '--output', $outputFile,
            $filePath,
        ];

        $process = new Process($command);
        $process->run();

        $this->summary()
            ->addStepWithProcess($process->isSuccessful(), $process, $password);

        return $process->isSuccessful();
    }

    /**
     * Checks if the required tools or libraries are installed for this adapter.
     *
     * @return bool
     */
    public function isSupportedEnvironment(): bool
    {
        $command = [$this->bin, '--version'];

        $process = new Process($command);
        $process->run();

        return $process->isSuccessful();
    }
}
