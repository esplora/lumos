<?php

namespace Esplora\Lumos\Concerns;

trait SupportsFileSize
{
    /**
     * Checks if the handler supports the archive format using file size limit.
     *
     * @param string $filePath Path to the file to check.
     *
     * @return bool Returns true if the file size is within the allowed limit, false otherwise.
     */
    public function canSupport(string $filePath): bool
    {
        return filesize($filePath) <= $this->getMaxFileSize();
    }

    /**
     * Returns the maximum allowed file size.
     *
     * @return int Maximum file size in bytes.
     */
    abstract protected function getMaxFileSize(): int;
}
