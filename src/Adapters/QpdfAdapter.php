<?php

namespace Esplora\Lumos\Adapters;

use Esplora\Lumos\Concerns\Decryptable;
use Esplora\Lumos\Concerns\SupportsMimeTypes;
use Esplora\Lumos\Contracts\AdapterInterface;
use Symfony\Component\Process\Process;

class QpdfAdapter implements AdapterInterface
{
    use Decryptable, SupportsMimeTypes;

    public function __construct(protected string $bin = 'qpdf') {}

    /**
     * Returns the list of supported MIME types.
     *
     * @return array<string> Array of MIME types supported by this handler.
     */
    protected function supportedMimeTypes(): array
    {
        return [
            'application/pdf',
        ];
    }

    /**
     * Attempts to decrypt the PDF file with the provided password.
     *
     * @param string      $filePath    Path to the PDF file.
     * @param string      $destination Path where the unlocked PDF will be saved.
     * @param string|null $password    Password to attempt for unlocking the PDF.
     *
     * @return bool Returns true if decryption was successful, false otherwise.
     */
    protected function tryDecrypting(string $filePath, string $destination, ?string $password = null): bool
    {
        $command = [
            $this->bin,
            '--password='.$password,
            '--decrypt',
            $filePath,
            $destination.basename($filePath),
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
     * @return bool Returns true if the environment is properly configured, false otherwise.
     */
    public function isSupportedEnvironment(): bool
    {
        $command = [$this->bin, '--version'];

        $process = new Process($command);
        $process->run();

        return $process->isSuccessful();
    }
}
