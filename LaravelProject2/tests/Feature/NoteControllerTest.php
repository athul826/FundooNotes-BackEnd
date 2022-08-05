<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    public function test_Successfull_createNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTAwNzA5LCJleHAiOjE2NTg1MDQzMDksIm5iZiI6MTY1ODUwMDcwOSwianRpIjoiWkhLWEV2d1JkeGZ0TDJxQSIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.F0ZtQ04RwWUAr17zKPsR_NgDi7HdnJstpdzh0JJS5gA'
        ])->json(
            'POST',
            '/api/createNote',
            [
                "title" => "nature",
                "description" => "greenary",
            ]
        );

        $response->assertStatus(200)->assertJson(['message' => 'Note created successfully']);
    }

    public function test_Unsuccessfull_createNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer "eyJ0eXAiOiJKV1QiLCJhbGciOi2562JIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTA4NDYxLCJleHAiOjE2NTg1MTIwNjEsIm5iZiI6MTY1ODUwODQ2MSwianRpIjoiTTZuMkk3QzVnNWcxNGpQeSIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.GAmaWTWjk-Ppl0bF7DdE1oMfALYBBI3Su_Mo2YC4STc"'
        ])->json(
            'POST',
            '/api/createnote',
            [
                "title" => "tc",
                "description" => "bangal",
            ]
        );

        $response->assertStatus(200)->assertJson(['message' => 'Invalid Authorization Token']);
    }
    /**
     * Successful Update Note By ID Test
     * Update a note using id and authorization token
     * 
     * @test
     */
    public function test_Successfull_updateNoteById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTA1MjM5LCJleHAiOjE2NTg1MDg4MzksIm5iZiI6MTY1ODUwNTIzOSwianRpIjoialZDYTNNclNEY2FyNlpJSyIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.M0Kqd9wji7XaWTk9Tt1Jxb5NVHhrhOsMqSGv9U1LvOs'
        ])->json(
            'POST',
            '/api/updateNoteById',
            [
                "note_id" => 1,
                "title" => "title updated",
                "description" => "description updated",
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Note Successfully updated']);
    }
    /**
     * UnSuccessful Update Note By ID Test
     * Update a note using id and authorization token
     * Passing wrong note or noteId which is not for this user, for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_updateNoteById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTA1MjM5LCJleHAiOjE2NTg1MDg4MzksIm5iZiI6MTY1ODUwNTIzOSwianRpIjoialZDYTNNclNEY2FyNlpJSyIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.M0Kqd9wji7XaWTk9Tt1Jxb5NVHhrhOsMqSGv9U1LvOs'
        ])->json(
            'POST',
            '/api/updateNoteById',
            [
                "note_id" => "12",
                "title" => "title has been updated",
                "description" => "updated",
            ]
        );
        $response->assertStatus(401)->assertJson(['message' => 'Notes Not Found']);
    }
    /**
     * Successful Delete Note By ID Test
     * Delete note by using id and authorization token
     * 
     * @test
     */
    public function test_Successfull_deleteNoteById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTA2OTM4LCJleHAiOjE2NTg1MTA1MzgsIm5iZiI6MTY1ODUwNjkzOCwianRpIjoicGtlZ29ucFcxWG9XOGZMRCIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.tKTiqdj-rbZFjG6qu5ICHhshSWPjhGWbFymlG6X3iMg'
        ])->json(
            'POST',
            '/api/deleteNoteById',
            [
                "note_id" => 12,
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Note deleted Successfully']);
    }
    /**
     * UnSuccessful Delete Note By ID Test
     * Delete note by using id and authorization token
     * Passing wrong note or noteId which is not for this user, for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_deleteNoteById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTA2OTM4LCJleHAiOjE2NTg1MTA1MzgsIm5iZiI6MTY1ODUwNjkzOCwianRpIjoicGtlZ29ucFcxWG9XOGZMRCIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.tKTiqdj-rbZFjG6qu5ICHhshSWPjhGWbFymlG6X3iMg'
        ])->json(
            'POST',
            '/api/deleteNoteById',
            [
                "note_id" => "15",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Notes Not Found']);
    }
    /**
     * Successful Add NoteLabel Test
     * Add NoteLabel using the label_id, note_id and authorization token
     * 
     * @test
     */
    public function test_Successfull_addNoteLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTA2OTM4LCJleHAiOjE2NTg1MTA1MzgsIm5iZiI6MTY1ODUwNjkzOCwianRpIjoicGtlZ29ucFcxWG9XOGZMRCIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.tKTiqdj-rbZFjG6qu5ICHhshSWPjhGWbFymlG6X3iMg'
        ])->json(
            'POST',
            '/api/addNoteLabel',
            [
                "note_id" => 14,
                "label_id" => 8,
            ]
        );
        $response->assertStatus(200)->assertJson(['message' => 'Label and note added Successfully']);
    }

    /**
     * UnSuccessful Add NoteLabel Test
     * Add NoteLabel using the label_id, note_id and authorization token
     * Using wrong label_id or note_id which is not of this user,
     * for this test
     * 
     * @test
     */
    public function test_Unsuccessfull_addNoteLabel()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTA4NDYxLCJleHAiOjE2NTg1MTIwNjEsIm5iZiI6MTY1ODUwODQ2MSwianRpIjoiTTZuMkk3QzVnNWcxNGpQeSIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.GAmaWTWjk-Ppl0bF7DdE1oMfALYBBI3Su_Mo2YC4STc'
        ])->json(
            'POST',
            '/api/addNoteLabel',
            [
                "note_id" => 15,
                "label_id" => 10,
            ]
        );
        $response->assertStatus(409)->assertJson(['message' => 'Note Already have a label']);
    }
    /**
     * for Successfull Search of note
     * to given anything
     * @test
     */
    public function test_Successfull_SearchNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTA4NDYxLCJleHAiOjE2NTg1MTIwNjEsIm5iZiI6MTY1ODUwODQ2MSwianRpIjoiTTZuMkk3QzVnNWcxNGpQeSIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.GAmaWTWjk-Ppl0bF7DdE1oMfALYBBI3Su_Mo2YC4STc'
        ])->json(
            'POST',
            '/api/searchNotes',
            [
                "search" => "php"
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Fetched Notes Successfully']);
    }

    /**
     * @test
     * for UnSuccessfull Search of note
     * to given anything
     */
    public function test_Unsuccessfull_SearchNote()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'J0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjU4NTA4NDYxLCJleHAiOjE2NTg1MTIwNjEsIm5iZiI6MTY1ODUwODQ2MSwianRpIjoiTTZuMkk3QzVnNWcxNGpQeSIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.GAmaWTWjk-Ppl0bF7DdE1oMfALYBBI3Su_Mo2YC4STc'
        ])->json(
            'POST',
            '/api/searchNotes',
            [
                "search" => "yyyyyssreea"
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'No results']);
    }
}
