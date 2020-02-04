<?php declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use App\User;
use DB;




class ResetPasswordControllerUnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function setUp(): void
    {
        parent::setUp();

        $this->controller = new ResetPasswordController;

        $this->user = factory(\App\User::class, 1)->create()->first();

        $this->request = new ChangePasswordRequest(
            [
                'email'    => $this->user->email,
                'password' => $this->faker->word,
                'token'    => $this->faker->word,
            ]
        );

    }//end setUp()


    public function testResetPasswordSendEmailFailed()
    {
        $request = new Request(
            [
                'email'    => '',
                'password' => '',
                'token'    => '',
            ]
        );
        $response = $this->controller->resetPassword($request);
        $this->assertEquals('Email could not be sent to this email address.', json_decode($response->getContent())->error);
        $this->assertEquals(500, $response->getStatusCode());
    
    }


    public function testResetPasswordSendEmailSucessfull()
    {
        $request = new Request(
            [
                'email'    => $this->user->email
            ]
        );
        $response = $this->controller->resetPassword($request);
        $this->assertEquals('The email to reset the password was sent successfully.', json_decode($response->getContent())->message);
        $this->assertEquals(200, $response->getStatusCode());

    }


    public function testNewPasswordFailedEmailOrTokenIncorrect()
    {
        $response = $this->controller->callResetPassword($this->request);
        $this->assertEquals('The token or email is incorrect.', json_decode($response->getContent())->error);
        $this->assertEquals(500, $response->getStatusCode());
    
    }//end testNewPasswordFailedEmailOrTokenIncorrect()


    public function testNewPasswordSuccessfull()
    {
        $request = new Request(
            [
                'email'    => $this->user->email
            ]
        );
        $this->controller->resetPassword($request);

        $passwordReset = DB::table('password_resets')->where(
            [
                'email' => $this->user->email,
            ]
        )->first();

        $this->request['email'] = $passwordReset->email;
        $this->request['token'] = $passwordReset->token;

        $response = $this->controller->callResetPassword($this->request);
        $this->assertEquals('The email to reset the password was sent successfully.', json_decode($response->getContent())->message);
        $this->assertEquals(200, $response->getStatusCode());

    }//end testNewPasswordSuccessfull()


}//end class
