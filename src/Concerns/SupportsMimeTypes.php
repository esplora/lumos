<?php

namespace Esplora\Lumos\Concerns;

use Illuminate\Support\Str;
use Symfony\Component\Mime\MimeTypes;

trait SupportsMimeTypes
{
    /**
     * Checks if the handler supports the archive format using Symfony Mime.
     *
     * @param string $filePath Path to the archive to check.
     *
     * @return bool Returns true if the archive format is supported, false otherwise.
     */
    public function canSupport(string $filePath): bool
    {
        $mimeTypesToCheck = [];

        if (! Str::of($filePath)->contains('/__MACOSX')) {
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $mimeTypesToCheck = MimeTypes::getDefault()->getMimeTypes($fileExtension);
        }

        $mimeTypesToCheck[] = (new MimeTypes)->guessMimeType($filePath);

        return collect($mimeTypesToCheck)->contains(
            fn (string $mimeType) => in_array($mimeType, $this->supportedMimeTypes(), true)
        );
    }

    /**
     * Returns the list of supported MIME types.
     *
     * @return array<string> Array of MIME types supported by this handler.
     */
    abstract protected function supportedMimeTypes(): array;
}
