<?php

namespace App\Http\Controllers\API\UserPanel;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Resources\Models\User as UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only('getCurrentUser');
    }

    public function getCurrentUser(Request $request)
    {
        return $this->sendResponse([
            'user' => new UserResource($request->user()->load('tenant')),
        ], 'Usuario logueado.');
    }
}
