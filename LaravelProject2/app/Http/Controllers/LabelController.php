<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Labels;
use App\Exceptions\FundooNotesException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

//use PhpParser\Node\Stmt\Labels;

class LabelController extends Controller
{
    /**
     *   @OA\Post(
     *   path="/api/createLabel",
     *   summary="create label",
     *   description="create label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"labelname"},
     *               @OA\Property(property="labelname", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Label Added Sucessfully"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * 
     * This function takes User access token and 
     * checks if it is authorised or not.
     * If authorised and no label with same name,
     * then a new label is created.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function createLabel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'labelname' => 'required|string|between:2,20',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->tojson(), 400);
            }
            $currentUser = JWTAuth::authenticate($request->token);
            $user_id = $currentUser->id;

            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundooNotesException('Invalid Authorization Token', 401);
            } else {
                $label = Labels::create([
                    'labelname' => $request->labelname,
                    'user_id' => $user_id,
                ]);
                Log::info('Labels successfully created');
                return response()->json([
                    'status' => 200,
                    'message' => 'Labels successfully created',
                    'label' => $label
                ]);
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

    /** @OA\Get(
     * path="/api/getLableById",
     * summary="get Lable By Id",
     * description="get Label For an Particular User",
     * @OA\RequestBody(),
     *   @OA\Response(response=200, description="All label are found Successfully"),
     *   @OA\Response(response=404, description="Label Not Found"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   security={
     *       {"Bearer": {}}
     *   }
     * )
     * This function takes access token and note id and finds
     * if there is any note existing on that User id and label id if so
     * it successfully returns that label id
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function getLableById(Request $request)
    {
        try {

            $validator = Validator::make($request->only('id'), [
                'id' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid'], 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);

            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundooNotesException('Invalid Authorization Token', 401);
            }

            $currentid = $currentUser->id;
            $label = Labels::where('user_id', $currentid)->where('id', $request->id)->first();

            if (!$label) {
                Log::info('Label Not Found');
                throw new FundooNotesException('Label Not Found', 404);
            } else {
                return response()->json(['label' => $label], 201);
            }
        } catch (FundooNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    public function getAllLabel(Request $request)
    {
        try {
            $user = JWTAuth::authenticate($request->token);
            $label = Labels::all();

            if (!$user) {
                Log::error('Invalid Authorization Token');
                throw new FundooNotesException('Invalid Authorization Token', 401);
            }
            $label = Labels::where('user_id', $user->id)->get();

            if (!$label) {
                Log::error('Label Not Found');
                throw new FundooNotesException('Label Not Found', 404);
            } else {
                Log::info('Label Retrived Successfully');
                return response()->json([
                    'status' => 200,
                    'label' => $label
                ]);
            }
        } catch (FundooNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
    /**
     *   @OA\Post(
     *   path="/api/updateLabelById",
     *   summary="update label",
     *   description="update user label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id","labelname"},
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="labelname", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Label Updated Successfully"),
     *   @OA\Response(response=404, description="Please enter valid id"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * 
     * This function takes the User access token and label id which
     * user wants to update and finds the label id if it is existed
     * or not if so, updates it successfully.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateLabelById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'updated_label' => 'required|string|between:2,30',
                'id' => 'required|integer',
            ]);
            // if ($validator->fails()) {
            //     return response()->json($validator->errors()->toJson(), 400);
            // }
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid'], 400);
            }

            $user = JWTAuth::authenticate($request->token);

            if (!$user) {
                Log::error('Invalid Authorization Token');
                throw new FundooNotesException('Invalid Authorization Token', 401);
            }

            $label = Labels::where('user_id', $user->id)->where('id', $request->id)->first();
            // return response()->json(['label' => $label], 200);

            if (!$label) {
                Log::error('Label Not Found');
                throw new FundooNotesException('Label Not Found', 404);
            }

            $label->labelname = $request->updated_label;
            // $label->user_id = $request->id;
            $label->save();

            return response()->json([
                'message' => 'label updated successfully',
                'label' => $label,
            ], 201);
        } catch (FundooNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
    /**
     *   @OA\Post(
     *   path="/api/deleteLabelById",
     *   summary="Delete Label",
     *   description="Delete User Label",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="integer"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="Label Successfully Deleted"),
     *   @OA\Response(response=404, description="Enter valid id"),
     *   @OA\Response(response=401, description="Invalid Authorization Token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     *
     * This function takes the User access token and label id.
     * Authenticate the user and Find the label id if it is existed
     * Delete label if user is Authenticated and label is present.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    function deleteLabelById(Request $request)
    {
        try {

            $validator = Validator::make($request->only('id'), [
                'id' => 'required|integer'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid'], 400);
            }

            $currentUser = JWTAuth::authenticate($request->token);

            if (!$currentUser) {
                Log::error('Invalid Authorization Token');
                throw new FundooNotesException('Invalid Authorization Token', 401);
            }

            $label = Labels::where('id', $request->id)->first();

            if (!$label) {
                Log::error('Label Not Found');
                throw new FundooNotesException('Label Not Found', 404);
            } else {

                $label->delete($label->id);
                return response()->json([
                    'mesaage' => 'label deleted Successfully',
                ], 200);
            }
        } catch (FundooNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
}
