<?php

namespace App\Http\Controllers\Api\V1;

use Log;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['getUsersWhoHasEntries']]);
    }

    public function getEntries($userId)
    {
        try {
            $author = User::findOrFail($userId);

            $entries = $author->entries();

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

    public function getUsersWhoHasEntries()
    {
        try {
            $authors = User::whereHas('entries')
                ->get();

            return response()->json($authors);
        } catch(Exception $e) {
            return response()->json(
                [
                    'errorMessage' => $e->getMessage()
                ],
                400
            );
        }
    }
}
