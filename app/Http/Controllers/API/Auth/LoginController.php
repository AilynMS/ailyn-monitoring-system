<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController as Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth:api')->only('logout');
    }

    /**
     * @psalm-suppress all
     */
    public function login(Request $request)
    {
        if ($request['validatecaptcha'] == false) {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);
        } else {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
                'g-recaptcha-response' => 'recaptcha',
            ]);
        }

        $user = User::where('email', $validated['email'])->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            return $this->sendError('Cuenta no verificada', ['error' => 'Esta cuenta aun no ha sido activada.'], 401);
        }

        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            $success['token'] = $user->createToken('MyApp')->accessToken;

            $success['name'] = $user->name;

            return $this->sendResponse($success, 'Usuario logueado con éxito.');
        } else {
            return $this->sendError('Credenciales invalidas.', ['error' => 'Las credenciales enviadas no concuerdan con nuestros registros.'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->sendResponse([], 'Log out exitoso!');
    }
}
