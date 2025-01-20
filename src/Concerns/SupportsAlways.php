<?php

namespace Esplora\Lumos\Concerns;

trait SupportsAlways
{
    /**
     * Always returns true, regardless of the file path provided.
     *
     * @param string $filePath The file path to check.
     *
     * @return bool Always returns true.
     */
    public function canSupport(string $filePath): bool
    {
        return true;
    }
}
