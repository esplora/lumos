<?php

namespace Esplora\Lumos\Concerns;

trait SupportsFileExtensions
{
    /**
     * Checks if the file extension is allowed.
     *
     * @param string $filePath Path to the file to check.
     *
     * @return bool Returns true if the file extension is allowed, false otherwise.
     */
    public function canSupport(string $filePath): bool
    {
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        return in_array($fileExtension, $this->allowedExtensions(), true);
    }

    /**
     * Returns the list of allowed file extensions.
     *
     * @return array<string> Array of allowed file extensions.
     */
    abstract protected function allowedExtensions(): array;
}
