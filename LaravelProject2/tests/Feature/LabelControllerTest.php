<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabelControllerTest extends TestCase
{
    /**
     * Successful Create Label Test
     * Create a label using label_name and authorization token for a user
     * 
     * @test
     */

    public function test_Successfull_createLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NjUxMjk4LCJleHAiOjE2NTg2NTQ4OTgsIm5iZiI6MTY1ODY1MTI5OCwianRpIjoiNlZ6U05QU0hWcEc3dXNhbSIsInN1YiI6IjE1IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.btJx9JyrgnOuicZ_VO10ucp5Bw458mwTMpytPol7dTw'
        ])->json(
            'POST',
            '/api/createLabel',
            [
                "labelname" => "Label",
            ]
        );

        $response->assertStatus(200)->assertJson(['message' => 'Labels successfully created']);
    }

    /**
     * UnSuccessful Create Label Test
     * Create a label using label_name and authorization token for a user
     * Using existing label name for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_createLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJ2KV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NjUxMjk4LCJleHAiOjE2NTg2NTQ4OTgsIm5iZiI6MTY1ODY1MTI5OCwianRpIjoiNlZ6U05QU0hWcEc3dXNhbSIsInN1YiI6IjE1IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.btJx9JyrgnOuicZ_VO10ucp5Bw458mwTMpytPol7dTw'
        ])->json(
            'POST',
            '/api/createlabel',
            [
                "labelname" => "new label",
            ]
        );

        $response->assertStatus(401)->assertJson(['message' => 'Invalid Authorization Token']);
    }
    /**
     * Successful Update Label Test
     * Update label using label_id, label_name and authorization
     * 
     * @test
     */
    public function test_Successfull_updateLabelById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NjUxMjk4LCJleHAiOjE2NTg2NTQ4OTgsIm5iZiI6MTY1ODY1MTI5OCwianRpIjoiNlZ6U05QU0hWcEc3dXNhbSIsInN1YiI6IjE1IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.btJx9JyrgnOuicZ_VO10ucp5Bw458mwTMpytPol7dTw'
        ])->json(
            'POST',
            '/api/updateLabelById',
            [
                "label_id" => 12,
                "labelname" => "harddisk",
            ]
        );

        $response->assertStatus(200)->assertJson(['message' => 'label updated successfully']);
    }

    /**
     * UnSuccessful Update Label Test
     * Update label using label_id, label_name and authorization
     * Using existing label name for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_updateLabelById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NjUxMjk4LCJleHAiOjE2NTg2NTQ4OTgsIm5iZiI6MTY1ODY1MTI5OCwianRpIjoiNlZ6U05QU0hWcEc3dXNhbSIsInN1YiI6IjE1IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.btJx9JyrgnOuicZ_VO10ucp5Bw458mwTMpytPol7dTw'
        ])->json(
            'POST',
            '/api/updateLabelById',
            [
                "id" => 11,
                "labelname" => "ch",
            ]
        );

        $response->assertStatus(404)->assertJson(['message' => 'Label Not Found']);
    }
    /**
     * Successful Delete Label Test
     * Delete Label using label_id and authorization token
     * @test
     */
    public function test_Successfull_DeleteLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NjUxMjk4LCJleHAiOjE2NTg2NTQ4OTgsIm5iZiI6MTY1ODY1MTI5OCwianRpIjoiNlZ6U05QU0hWcEc3dXNhbSIsInN1YiI6IjE1IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.btJx9JyrgnOuicZ_VO10ucp5Bw458mwTMpytPol7dTw'
        ])->json(
            'POST',
            '/api/deleteLabelById',
            [
                "id" => 13,
            ]
        );

        $response->assertStatus(200)->assertJson(['message' => 'label deleted Successfully']);
    }

    /**
     * UnSuccessful Delete Label Test
     * Delete Label using label_id and authorization token
     * Giving wrong label_id for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_DeleteLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU2NjEzMDU1LCJleHAiOjE2NTY2MTY2NTUsIm5iZiI6MTY1NjYxMzA1NSwianRpIjoiUDY2bUNNNWZRSzJIdFBsMCIsInN1YiI6IjkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.wPAmxwMFtHqfa-v6ZU8S1hiMXZYiAeOv4StfNyv7EVY'
        ])->json(
            'POST',
            '/api/deleteLabelById',
            [
                "id" => 20,
            ]
        );

        $response->assertStatus(404)->assertJson(['message' => 'Label Not Found']);
    }
}
