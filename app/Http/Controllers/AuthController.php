<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('username', 'password'))) {
            $user = Auth::user();

            $token = $user->createToken('MyApp')->accessToken;
            return response()->json(
                [
                    'token' => $token,
                    'user'  => $user,
                ],
                200
            );
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }

    }//end login()


}//end class
