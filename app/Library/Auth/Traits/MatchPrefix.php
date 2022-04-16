<?php

namespace App\Library\Auth\Traits;

trait MatchPrefix
{
    /**
     * Match if login is preceeded with prefix
     * @param string $login
     * @param string $prefix
     * @return boolean
     */
    protected function matchPrefix($login, $prefix)
    {
        $matches = [];
        if (preg_match('/^' . $prefix . '/', $login, $matches)) {
            return true;
        }

        return false;
    }
}
