<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        $messages  = [
            'email.required'            => 'Ingrese su correo electrónico',
            'email.email'               => 'El correo electrónico es inválido',
            'email.exists'              => 'El correo electrónico no existe',
            'email.unique'              => 'El correo electrónico ya existe',
            'password.required'         => 'Ingrese su contraseña',
            'confirm_password.required' => 'Debe confirmar su contraseña',
            'password.min'              => 'La contraseña debe tener minimo 8 caracteres',
        ];
        $validator = Validator::make(
            $request->all(),
            [
                'email'            => 'required|email|unique:users',
                'password'         => 'required|min:8',
                'confirm_password' => 'required|same:password',
            ],
            $messages
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $input = $request->all();
        $input['password'] = bcrypt($request->get('password'));
        $user  = User::create($input);
        $token = $user->createToken('MyApp')->accessToken;

        return response()->json(
            [
                'token' => $token,
                'user'  => $user,
            ],
            200
        );

    }//end register()


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
