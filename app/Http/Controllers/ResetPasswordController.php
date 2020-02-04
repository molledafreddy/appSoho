<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\AppResponseTrait;
use Validator;

use App\Http\Requests\ChangePasswordRequest;
use App\Mail\ResetPassword;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Mail;
use Response;

class ResetPasswordController extends ApiController
{
    /**
     * @api           {put} api/v2/users/reset/password/:id envio de correo para reseteo de clave
     * @apiName       resetPassword
     * @apiPermission admin
     * @apiGroup      AdminUsers
     *
     * @apiParam {Integer} id id del usuario a actualizar
     *
     * @apiSuccess {String} status de la respuesta, normalmente ok cuando esta todo bien.
     * @apiSuccess {String} message mensaje para el front-end
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 GET
     *     {
     *       "status": "ok",
     *       "message":El correo para restablecer la contraseña se envio con exíto
     *     }
     *
     * @apiError invalid_credentials The Authentication failed.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "error": "invalid_credentials"
     *     }
     */
    public function resetPassword(Request $request)
    {
        if (!$this->validateEmail($request->email)) {
            return $this->failedResponse();
        }

        $this->send($request->email);

        return $this->successResponse();

    }//end resetPassword()


    public function validateEmail($email)
    {
        return !!User::where('email', $email)->first();
    }

    public function failedResponse()
    {
        return $this->errorResponse('Email could not be sent to this email address.',500);
    }

    public function send($email)
    {
        $token = $this->createToken($email);
        $time = config('auth.passwords.users.expire');
        $link = url("/#/reset-password/" . $token);

        Mail::to($email)->send(new ResetPassword($link, $time));
    }

    public function createToken($email)
    {
        $oldToken = DB::table('password_resets')->where('email', $email)->first();

        if ($oldToken) {
            return $oldToken->token;
        }

        $length = 60;

        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $token            = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[rand(0, ($charactersLength - 1))];
        }

        $this->saveToken($token, $email);

        return $token;

    }//end createToken()


    public function saveToken($token, $email)
    {
        DB::table('password_resets')->insert(
            [
                'email'      => $email,
                'token'      => $token,
                'created_at' => Carbon::now(),
            ]
        );

    }//end saveToken()


    public function successResponse()
    {
        return response()->json(['message' => 'The email to reset the password was sent successfully.'], 200);
    }//end successResponse()


    /**
     * @api           {put} api/v2/users/call-reset-password permite resetear la clave
     * @apiName       callResetPassword
     * @apiPermission admin
     * @apiGroup      AdminUsers
     *
     * @apiParam {String} email correo eectronico del usuario
     * @apiParam {String} token token de validacion
     * @apiParam {String} password clave que se restituye
     *
     * @apiSuccess {String} status de la respuesta, normalmente ok cuando esta todo bien.
     * @apiSuccess {String} message mensaje para el front-end
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 GET
     *     {
     *       "status": "ok",
     *       "message":El correo para restablecer la contraseña se envio con exíto
     *     }
     *
     * @apiError invalid_credentials The Authentication failed.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *     {
     *       "error": "invalid_credentials"
     *     }
     */
    public function callResetPassword(ChangePasswordRequest $request)
    {
        return $this->getPasswordResetTabletRow($request)->count() > 0 ? $this->changePassword($request) : $this->tokenNotFoundResponse();

    }//end callResetPassword()


    private function getPasswordResetTabletRow($request)
    {
        return $result = DB::table('password_resets')->where(
            [
                'email' => $request->email,
                'token' => $request->token,
            ]
        );

    }//end getPasswordResetTabletRow()


    private function changePassword($request)
    {
        $user = User::whereEmail($request->email)->first();

        $user->update(
            [
                'password' => bcrypt($request->password),
            ]
        );

        $this->getPasswordResetTabletRow($request)->delete();

        return response()->json(['message' => 'The email to reset the password was sent successfully.'], 200);

    }//end changePassword()


    private function tokenNotFoundResponse()
    {
        return $this->errorResponse('The token or email is incorrect.',500);
    }//end tokenNotFoundResponse()


}//end class
