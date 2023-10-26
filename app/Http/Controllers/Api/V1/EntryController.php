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
        $this->middleware('auth:api', ['except' => ['search']]);
    }

    public function search(Request $request)
    {
        try {
            $entries = Entry::query();

            if ($keyword = $request->input('keyword', NULL)) {
                $entries->where('title', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('content', 'LIKE', '%'.$keyword.'%');
            }

            if ($authorId = $request->input('author_id', NULL)) {
                $entries->where('user_id', $authorId);
            }

            $entries = $entries->with(['author'])
                ->orderBy('created_at', 'DESC')
                ->get();

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

    public function getOne($entryId)
    {
        try {
            $entry = Entry::with(['author'])
                ->where('id', $entryId)
                ->firstOrFail();

            return response()->json($entry);
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
        ];

        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'content' => 'required',
            ],
            $messages
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();

        try {
            $author = auth()->user();

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
