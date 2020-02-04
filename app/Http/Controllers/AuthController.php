<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Lcobucci\JWT\Parser;
use Carbon\Carbon;

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
                    'token_type' => 'Bearer',
                    'user'  => $user,
                ],
                200
            );
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }

    }//end login()


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);

        // dd($request->user());
        // $request->user()->token()->revoke();
        // $value = $request->token;
        // $id = (new \Parser())->parse($value)->getHeader('jti');
        // dd($value);
        // $token = $request->user()->tokens->find($id);
        // $token->revoke();

        // return response()->json(['message' => 
        //     'Successfully logged out']);
    }


}//end class
