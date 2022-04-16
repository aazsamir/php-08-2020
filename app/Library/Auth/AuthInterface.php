<?php

namespace App\Library\Auth;

/**
 * Interface used in authentication across systems
 */
interface AuthInterface
{
    /**
     * Returns true if login match system requirements
     * @param string $login
     * @return boolean
     */
    public function match($login);

    /**
     * Request external library for auth
     * @param string $login
     * @param string $password
     * @return boolean
     */
    public function action($login, $password);
}
