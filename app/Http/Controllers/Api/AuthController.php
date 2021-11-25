<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {

    }

    /**
     * login user and return user's permissions, user's role
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            'email'         => $request->input('email', ''),
            'password'      => $request->input('password', ''),
        ];

        // authenticate user
        if (!Auth::attempt($credentials)) {
            return $this->sendError('Invalid login details', Response::HTTP_UNAUTHORIZED);
        }

        // get user if authenticated
        $user = Auth::user();

        // create access token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse([
            'access_token'  => $token,
            'name'          => $user->name,
            'email'         => $user->email,
        ]);
    }

}
