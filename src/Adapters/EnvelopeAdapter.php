<?php

namespace Esplora\Lumos\Adapters;

use Cosmira\Envelope\Envelope;
use Esplora\Lumos\Concerns\Decryptable;
use Esplora\Lumos\Concerns\SupportsMimeTypes;
use Esplora\Lumos\Contracts\AdapterInterface;

class EnvelopeAdapter implements AdapterInterface
{
    use Decryptable, SupportsMimeTypes;

    public function __construct(protected string $bin = '') {}

    /**
     * Returns the list of supported MIME types.
     *
     * @return array<string> Array of MIME types supported by this handler.
     */
    public function supportedMimeTypes(): array
    {
        return [
            'message/rfc822',
            'application/eml',
            'application/x-eml',
        ];
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
        $emailContent = file_get_contents($filePath);

        $mail = Envelope::fromString($emailContent);

        $attachments = $mail->attachments();

        foreach ($attachments as $attachment) {
            file_put_contents($destination.'/'.$attachment['name'], $attachment['content']);
        }

        $this->summary()->addStep(true, [
            'password' => $password,
        ]);

        return true;
    }

    /**
     * Checks if the required tools or libraries are installed for this adapter.
     *
     * @return bool
     */
    public function isSupportedEnvironment(): bool
    {
        return class_exists(\Cosmira\Envelope\Envelope::class);
    }
}
