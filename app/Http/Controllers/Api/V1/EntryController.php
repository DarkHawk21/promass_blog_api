<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Entry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
