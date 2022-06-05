<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController as Controller;
use App\Models\Auth\PasswordReset;
use App\Models\User;
use App\Notifications\Auth\PasswordResetRequest;
use App\Notifications\Auth\PasswordResetSuccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
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

    /**
     * Create token password reset
     *
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'g-recaptcha-response' => 'recaptcha',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->sendResponse([], 'Le hemos enviado un correo con el enlace para restaurar su contraseña!');
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60),
            ]
        );

        if ($user && $passwordReset) {
            $user->notify(new PasswordResetRequest($passwordReset->token));
        }

        return $this->sendResponse([], 'Le hemos enviado un correo con el enlace para restaurar su contraseña!');
    }

    /**
     * Find token password reset
     *
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (! $passwordReset) {
            return $this->sendError('El token es invalido.');
        }

        if (Carbon::parse($passwordReset->updated_at)->addDays(3)->isPast()) {
            $passwordReset->delete();

            return $this->sendError('El token es invalido.');
        }

        return $this->sendResponse(['token' => $passwordReset], 'El token es valido');
    }

    /**
     * Reset password
     *
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => [
                'required',
                'string',
                'min:' . config('user_settings.validations.password.min'),
                'confirmed',
                'regex:' . config('user_settings.validations.password.regex'),
            ],
            'token' => 'required|string',
        ]);

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email],
        ])->first();

        if (! $passwordReset) {
            return $this->sendError('El token es invalido.');
        }

        $user = User::where('email', $passwordReset->email)->first();

        // TODO: Check if this error message should be sended
        if (! $user) {
            return $this->sendError('No se pudo encontrar un usuario con ese email.');
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess());

        return $this->sendResponse([], 'Contraseña restaurada con éxito!');
    }
}
