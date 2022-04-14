<?php

namespace App\Http\Controllers;

use External\Bar\Auth\LoginService;
use External\Baz\Auth\Authenticator;
use External\Baz\Auth\Responses\Success;
use External\Foo\Auth\AuthWS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $post = $request->only(['login', 'password']);
        //validate request
        $validation = Validator::make($post, [
            'login' => 'required',
            'password' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'status' => 'failure',
                // 'validation' => $validation->failed(),
            ]);
        }
        //it would look much better, if handled by external class
        //define primary actions (match, for matching what system user is trying to log in, and action, to try log in) to use, in order to auth user
        $actions = [
            'foo' => [
                'match' => function ($login) {
                    $matches = [];
                    if (preg_match('/^FOO/', $login, $matches)) {
                        return true;
                    }

                    return false;
                },
                'action' => function ($login, $password) {
                    $auth_ws = new AuthWS();
                    try {
                        $return = $auth_ws->authenticate($login, $password);
                        return true;
                    } catch (\Exception $e) {
                        return false;
                    }
                },
            ],
            'bar' => [
                'match' => function ($login) {
                    $matches = [];
                    if (preg_match('/^BAR/', $login, $matches)) {
                        return true;
                    }

                    return false;
                },
                'action' => function ($login, $password) {
                    $login_service = new LoginService();
                    return $login_service->login($login, $password);
                },
            ],
            'baz' => [
                'match' => function ($login) {
                    $matches = [];
                    if (preg_match('/^BAZ_/', $login, $matches)) {
                        return true;
                    }

                    return false;
                },
                'action' => function ($login, $password) {
                    $authenticator = new Authenticator();
                    $return = $authenticator->auth($login, $password);
                    if ($return instanceof Success) {
                        return true;
                    } else {
                        return false;
                    }
                },
            ]
        ];

        $result = false;

        $matched_system = null;;

        foreach ($actions as $system => $data) {
            $match = $data['match'];
            if ($match($post['login'])) {
                $action = $data['action'];
                $result = $action($post['login'], $post['password']);
                $matched_system = $system;
                break;
            }
        }

        if ($result) {
            return response()->json([
                'status' => 'success',
                'token' => $this->generateToken($post['login'], $matched_system),
            ]);
        }

        return response()->json([
            'status' => 'failure',
        ]);
    }

    /**
     * Generate Auth token, based on login and used system
     * @param string $login
     * @param string $system
     * @return string
     */
    protected function generateToken($login, $system)
    {
        return base64_encode(
            json_encode([
                'login' => $login,
                'system' => $system,
                'secret' => 'foo_bar_baz',
            ])
        );
    }
}
