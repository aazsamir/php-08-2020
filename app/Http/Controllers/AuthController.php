<?php

namespace App\Http\Controllers;

use App\Library\Auth\SystemAuth;
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
        $system_auth = SystemAuth::getInstance();
        $result = $system_auth->auth($post['login'], $post['password']);

        if ($result) {
            return response()->json([
                'status' => 'success',
                'token' => $system_auth->generateToken($post['login']),
            ]);
        }

        return response()->json([
            'status' => 'failure',
        ]);
    }
}
