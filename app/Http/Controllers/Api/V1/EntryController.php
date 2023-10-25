<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use Log;
use Exception;
use App\Models\Entry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class EntryController extends Controller
{
    /**
     * Create a new EntryController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    public function index()
    {
        try {
            $entries = Entry::get();

            return response()->json($entries);
        } catch(Exception $e) {
            return response()->json(
                [
                    'errorMessage' => $e->getMessage()
                ],
                400
            );
        }
    }

    public function storeOne(Request $request)
    {
        $messages = [
            'title.required' => "Se debe ingresar un título.",
            'content.required' => "Se debe ingresar un contenido.",
            'author_id.required' => "Se debe ingresar el identificador del autor."
        ];

        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'content' => 'required',
                'author_id' => 'required'
            ],
            $messages
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();

        try {
            $author = User::findOrFail($request->input('author_id'));

            $newEntry = Entry::create(
                [
                    'title' => $request->input('title'),
                    'content' => $request->input('content'),
                    'user_id' => $author->id
                ]
            );

            DB::commit();

            return response()->json($newEntry);
        } catch(Exception $e) {
            Log::info($e);

            DB::rollBack();

            return response()->json(
                [
                    'errorMessage' => $e->getMessage()
                ],
                400
            );
        }
    }
}
