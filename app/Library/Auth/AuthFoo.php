<?php

namespace App\Library\Auth;

use App\Library\Auth\Traits\MatchPrefix;
use External\Foo\Auth\AuthWS;

class AuthFoo implements AuthInterface
{
    use MatchPrefix;

    public function match($login)
    {
        return $this->matchPrefix($login, 'FOO');
    }

    public function action($login, $password)
    {
        $auth_ws = new AuthWS();
        try {
            $return = $auth_ws->authenticate($login, $password);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
