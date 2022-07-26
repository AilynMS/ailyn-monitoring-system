<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController as Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function verify($user_id, Request $request)
    {
        $url = config('app.client_url') . '/auth/login';

        if (! $request->hasValidSignature()) {
            $url .= '?success=false&error=expired_url';

            return redirect($url);
        }

        $user = User::findOrFail($user_id);

        if ($user->hasVerifiedEmail()) {
            $url .= '?verified=true';
            
            return redirect($url);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $url .= '?success=true';

        return redirect($url);
    }
}
