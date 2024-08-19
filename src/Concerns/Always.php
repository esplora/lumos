<?php

declare(strict_types=1);

namespace Esplora\Decompresso\Concerns;

trait Always
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
