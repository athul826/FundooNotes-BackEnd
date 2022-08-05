<?php

namespace App\Http\Controllers;

use App\Exceptions\FundooNotesException;
use App\Models\LabelNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Notes;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/createNote",
     *   summary="Create Note",
     *   description=" Create Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"title", "description"},
     *               @OA\Property(property="title", type="string"),
     *               @OA\Property(property="description", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="notes created successfully"),
     *   @OA\Response(response=400, description="Invalid authorization token"),
     *   security = {
     * {
     * "Bearer" : {}}}
     * )
     */
    function createNote(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|min:3|max:30',
                'description' => 'required|string|min:3|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);
            $user_id = $currentUser->id;

            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundooNotesException('Invalid Authorization Token', 401);
            } else {

                $note = Notes::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'user_id' => $user_id,

                ]);
                return response()->json([
                    'message' => 'Note created successfully',
                    'note' => $note
                ], 200);
            }
            Cache::remember('notes', 3600, function () {
                return DB::table('notes')->get();
            });
        } catch (FundooNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
    /** @OA\Post(
     * path="/api/getNoteById",
     * summary="Read Note",
     * description="Read Notes For an Particular User",
     * @OA\RequestBody(
     *    @OA\JsonContent(),
     *    @OA\mediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *           type="object",
     *           required={"id"},
     *           @OA\Property(property="id",type="integer"),
     *      ),
     *    ),
     *  ),
     *   @OA\Response(response=200, description="All Notes are found Successfully"),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   security={
     *       {"Bearer": {}}
     *   }
     * )
     * This function takes access token and note id and finds
     * if there is any note existing on that User id and note id if so
     * it successfully returns that note id
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function getNoteById(Request $request)
    {

        $validator = Validator::make($request->only('id'), [
            'id' => 'required|integer',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid'], 400);
        }

        $currentUser = JWTAuth::authenticate($request->token);

        if (!$currentUser) {
            return response()->json([
                'message' => 'Invalid Authorization Token',
            ], 401);
        }

        $currentid = $currentUser->id;
        //$note = Note::where('id', $request->id)->first();
        $note = Notes::where('user_id', $currentid)->where('id', $request->id)->first();

        if (!$note) {
            return response()->json([
                'message' => 'Invalid id'
            ], 401);
        } else {
            return response()->json(['note' => $note], 200);
        }
    }
    /**
     * @OA\Get(
     *      path="/api/getAllNotes",
     *      summary="get All Notes",
     *      description="get All Notes",
     * 
     *      @OA\Response(response=200, description="Notes found successfully"),
     *      @OA\Response(response=404, description="Invalid Autherization token"),
     * 
     *      security={
     *          {"Bearer":{}}
     * }
     * )
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    function getAllNotes(Request $request)
    {
        try {

            $currentUser = JWTAuth::authenticate($request->token);

            if (!$currentUser) {
                return response()->json([
                    'message' => 'Invalid Authorization Token',
                ], 401);
            }
            $notes = Notes::getAllNotes($currentUser);

            if (!$notes) {
                return response()->json([
                    'message' => 'No note created by this user',
                ], 401);
            } else {
                return response()->json([
                    'notes' => $notes,
                ], 200);
            }
        } catch (FundooNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
    /**
     *   @OA\Post(
     *   path="/api/updateNoteById",
     *   summary="update note",
     *   description="update note",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id","title","description"},
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="title", type="string"),
     *               @OA\Property(property="description", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note Updated Successfully"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes the User access token and
     * Note Id which user wants to update and 
     * finds the note id if it is existed or not. 
     * If it is existed then, updates it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateNoteById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|between:2,30',
                'description' => 'required|string|between:3,1000',
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user = JWTAuth::authenticate($request->token);

            if (!$user) {

                throw new FundooNotesException('Invalid Authorization Token', 401);
            }

            $note = Notes::where('user_id', $user->id)->where('id', $request->id)->first();

            if (!$note) {
                throw new FundooNotesException('Notes Not Found', 404);
            }

            $note->title = $request->title;
            $note->description = $request->description;
            $note->save();

            return response()->json([
                'status' => 200,
                'note' => $note,
                'mesaage' => 'Note Successfully updated',
            ]);
        } catch (FundooNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
    /**
     *   @OA\Delete(
     *   path="/api/deleteNoteById",
     *   summary="delete note",
     *   description="delete user note",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note successfully deleted"),
     *   @OA\Response(response=404, description="Notes not found"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes the User access token and note id which
     * user wants to delete and finds the note id if it is existed
     * or not if so, deletes it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function deleteNoteById(Request $request)
    {

        try {

            $validator = Validator::make($request->only('id'), [
                'id' => 'required|integer',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid'], 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);

            if (!$currentUser) {
                log::warning('Invalid Authorisation Token ');
                throw new FundooNotesException('Invalid Authorization Token', 401);
            }

            $note = Notes::where('id', $request->id)->first();

            if (!$note) {
                Log::error('Notes Not Found');
                throw new FundooNotesException('Notes Not Found', 404);
            } else {
                $note->delete($note->id);
                return response()->json([
                    'mesaage' => 'Note deleted Successfully',
                ], 200);
            }
        } catch (FundooNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
    /**
     *   @OA\Post(
     *   path="/api/addNoteLabel",
     *   summary="Add note label",
     *   description="Adiing note label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"label_id","note_id"},
     *               @OA\Property(property="label_id", type="string"),
     *               @OA\Property(property="note_id", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note successfully updated"),
     *   @OA\Response(response=402, description="Labels or Notes not found"),
     *   @OA\Response(response=401, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This function takes the User access token and note id which
     * user wants to update and finds the note id if it is existed
     * or not if so, updates it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function addNoteLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'note_id' => 'required',
            'label_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid authorization token'
            ], 401);
        }

        $labelnote = LabelNotes::where('note_id', $request->note_id)->where('label_id', $request->label_id)->first();
        if ($labelnote) {
            return response()->json([
                'status' => 400,
                'message' => 'Note Already have a label'
            ], 409);
        }

        //$notelabel = LabelNotes::createNoteLabel($request, $user->id);
        $labelnotes = LabelNotes::create([
            'user_id' => $user->id,
            'note_id' => $request->note_id,
            'label_id' => $request->label_id
        ]);
        log::info('Label created Successfully');
        return response()->json([
            'status' => 200,
            'message' => 'Label and note added Successfully',
            'notelabel' => $labelnotes,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/api/searchNotes",
     *   summary="Search Note",
     *   description=" Search Note ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"search"},
     *               @OA\Property(property="search", type="string")
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Note Fetched Sucessfully"),
     *   @OA\Response(response=404, description="Notes Not Found"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   security = {
     *      {"Bearer" : {}}
     *   }
     * )
     * 
     * This function takes the User access token and search key 
     * if the access token is valid, it returns all the notes 
     * which has given search key for that particular user.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function searchNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $searchKey = $request->input('search');
        $currentUser = JWTAuth::parseToken()->authenticate();

        if ($currentUser) {

            $usernotes = User::leftjoin('notes', 'notes.user_id', '=', 'users.id')
                ->select('users.id', 'notes.id', 'notes.title', 'notes.description')
                ->where([['notes.user_id', '=', $currentUser->id]])
                ->where('notes.user_id', '=', $currentUser->id)->Where('notes.title', 'like', '%' . $searchKey . '%')
                ->orWhere('notes.user_id', '=', $currentUser->id)->Where('notes.description', 'like', '%' . $searchKey . '%')
                ->get();

            if ($usernotes == '[]') {
                return response()->json([
                    'status' => 404,
                    'message' => 'No results'
                ], 404);
            }
            return response()->json([
                'status' => 201,
                'message' => 'Fetched Notes Successfully',
                'notes' => $usernotes
            ], 201);
        }
        Log::error('Invalid Authorization Token');
        return response()->json([
            'status' => 403,
            'message' => 'Invalid authorization token'
        ], 403);
    }
}
