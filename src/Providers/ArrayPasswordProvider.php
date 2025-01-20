<?php

namespace Esplora\Lumos\Providers;

use Esplora\Lumos\Contracts\PasswordProviderInterface;

class ArrayPasswordProvider implements PasswordProviderInterface
{
    /**
     * Constructor for the ArrayPasswordProvider class.
     *
     * @param array $passwords Array of passwords for extracting protected archives.
     */
    public function __construct(protected array $passwords) {}

    /**
     * Returns the list of passwords.
     *
     * Provides the array of passwords used for attempting extraction of password-protected archives.
     *
     * @return array An array containing password strings.
     */
    public function getPasswords(): array
    {
        return $this->passwords;
    }
}
