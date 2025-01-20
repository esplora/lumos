<?php

namespace Esplora\Lumos\Contracts;

interface PasswordProviderInterface
{
    /**
     * Returns a list of passwords for archive extraction.
     *
     * This method should return an iterable object containing password strings. These passwords will be used to attempt
     * extraction of password-protected archives. If an archive requires a password, this method should provide a list
     * of possible passwords to be tried sequentially.
     *
     * @return iterable Returns an iterable object (e.g., array or generator) containing password strings.
     */
    public function getPasswords(): iterable;
}
