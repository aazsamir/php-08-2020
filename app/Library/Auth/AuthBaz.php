<?php

namespace App\Library\Auth;

use App\Library\Auth\Traits\MatchPrefix;
use External\Baz\Auth\Authenticator;
use External\Baz\Auth\Responses\Success;

class AuthBaz implements AuthInterface
{
    use MatchPrefix;

    public function match($login)
    {
        return $this->matchPrefix($login, 'BAZ_');
    }

    public function action($login, $password)
    {
        $authenticator = new Authenticator();
        $return = $authenticator->auth($login, $password);
        if ($return instanceof Success) {
            return true;
        }

        return false;
    }
}
