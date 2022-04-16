<?php

namespace App\Library\Auth;

use App\Library\Auth\Traits\MatchPrefix;
use External\Bar\Auth\LoginService;

class AuthBar implements AuthInterface
{
    use MatchPrefix;

    public function match($login)
    {
        return $this->matchPrefix($login, 'BAR');
    }

    public function action($login, $password)
    {
        $login_service = new LoginService();
        return $login_service->login($login, $password);
    }
}
