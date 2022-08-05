<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function test_Successfull_Registration()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/register', [
                "firstname" => "deepak",
                "lastname" => "andra",
                "email" => "deepak@gmail.com",
                "password" => "deepak321",
                "password_confirmation" => "deepak321"
            ]);

        $response->assertStatus(201)->assertJson(['message' => 'User successfully registered']);
    }

    public function test_Unsuccessfull_register()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])
            ->json('POST', '/api/register', [
                "firstname" => "yadhu",
                "lastname" => "krishna",
                "email" => "rushi@gmail.com",
                "password" => "athul321",
                "password_confirmation" => "athul321"
            ]);
        $response->assertStatus(200)->assertJson(['message' => 'The email has already been taken']);
    }
    /**
     * Test for successful Login
     * Login the user by using the email and password as credentials
     * 
     * @test
     */
    public function test_Successfull_login()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json(
            'POST',
            '/api/login',
            [
                "email" => "rushi@gmail.com",
                "password" => "athul321"
            ]
        );
        $response->assertStatus(200)->assertJson(['success' => 'Login successful']);
    }

    /**
     * Test for Unsuccessfull Login
     * Login the user by email and password
     * Wrong password for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_login()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json(
            'POST',
            '/api/login',
            [
                "email" => "athultharol19943.com",
                "password" => "work232"
            ]
        );
        $response->assertStatus(400)->assertJson(['message' => 'Invalid credentials entered']);
    }

    /**
     * Test for Successfull Logout
     * Logout a user using the token generated at login
     * 
     * @test
     */
    public function test_Successfull_logout()
    { {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/logout', [
                "token" => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTEwNzEzLCJleHAiOjE2NTg1MTQzMTMsIm5iZiI6MTY1ODUxMDcxMywianRpIjoieUY2aUVTN2t4NlRCaTZKOCIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.AAqPig4O1Mjbk2Zp4OicjUbFmIlpIkWZodef-OuFgNM'
            ]);

            $response->assertStatus(200)->assertJson(['message' => 'User has been logged out']);
        }
    }

    /**
     * Test for unSuccessfull Logout
     * Logout a user using the token generated at login
     * Passing the wrong token for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_logout()
    { {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/logout', [
                "token" => 'eyJ0eXAiOiJKV1QiLCJde.hbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTEwNzEzLCJleHAiOjE2NTg1MTQzMTMsIm5iZiI6MTY1ODUxMDcxMywianRpIjoieUY2aUVTN2t4NlRCaTZKOCIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.AAqPig4O1Mjbk2Zp4OicjUbFmIlpIkWZodef-OuFgNM'
            ]);

            $response->assertStatus(401)->assertJson(['message' => 'Invalid token']);
        }
    }



    /**
     * Test for Successfull Forgot Password
     * Send a mail for forgot password of a registered user
     * 
     * @test
     */
    public function test_Successfull_forgotPassword()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/forgotPassword', [
            "email" => "yadulive333@gmail.com"
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'Reset link Sent to your Email']);
    }

    /**
     * Test for UnSuccessfull Forgot Password
     * Send a mail for forgot password of a registered user
     * Non-Registered email for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_forgotPassword()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/forgotPassword', [
            "email" => "yavu@gmail.com"
        ]);

        $response->assertStatus(402)->assertJson(['message' => 'Email is not registered']);
    }

    /**
     * Test for Successfull Reset Password
     * Reset password using the token and 
     * setting the new password to be the password
     * 
     * @test
     */
    public function test_Successfull_resetPassword()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/resetPassword', [
            "new_password" => "yadhu@1234",
            "password_confirmation" => "yadhu@1234",
            "token" => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTExNDMxLCJleHAiOjE2NTg1MTUwMzEsIm5iZiI6MTY1ODUxMTQzMSwianRpIjoiTFVaN3JiT0NnQjZiaXdWWCIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.pBqt3-oWxrKx2T-ffdTpRH3vsrrFVAUddYC4nMPWM-0'
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'Password Reset Successful']);
    }

    /**
     * Test for unSuccessfull Reset Password
     * Reset password using the token and 
     * setting the new password to be the password
     * Wrong token is passed for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_resetPassword()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/resetPassword', [
            "new_password" => "abcd@123656",
            "password_confirmation" => "abcd@123656",
            "token" => '5eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NikJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTExNDMxLCJleHAiOjE2NTg1MTUwMzEsIm5iZiI6MTY1ODUxMTQzMSwianRpIjoiTFVaN3JiT0NnQjZiaXdWWCIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.pBqt3-oWxrKx2T-ffdTpRH3vsrrFVAUddYC4nMPWM-0'
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Invalid Authorization Token']);
    }
}
