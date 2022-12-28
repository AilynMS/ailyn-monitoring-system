<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController as Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\Auth\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Monarobase\CountryList\CountryListFacade as Countries;

class RegisterController extends Controller
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
     * Get country lists in spanish and array format
     *
     * @param string $locale Lang code
     * @param string $format
     *
     * @return array The countries list
     */
    protected function getCountriesArray($locale = 'es', $format = 'php')
    {
        return Countries::getList($locale, $format);
    }

    public function getCountries()
    {
        return $this->sendResponse($this->getCountriesArray(), 'Países disponibles');
    }

    /**
     * Get all timezones in array format from configuration file
     *
     * @return array
     */
    public function getAllTimezonesArray()
    {
        return config('all_timezones');
    }

    public function getTimezones()
    {
        return $this->sendResponse($this->getAllTimezonesArray(), 'Zonas horarias disponibles');
    }

    public function register(Request $request)
    {
        if ($request['validatecaptcha'] == false) {
            $validated = $request->validate([
                'name' => 'required|string',
                'organization' => 'required|string|unique:tenants,name',
                'email' => 'required|string|email|unique:users,email',
                'password' => [
                    'required',
                    'string',
                    'min:' . config('user_settings.validations.password.min'),
                    'confirmed',
                    'regex:' . config('user_settings.validations.password.regex'),
                ],
                'terms' => 'required|accepted',
                'country' => [
                    'required',
                    'string',
                    Rule::in($this->getCountriesArray()),
                ],
            ]);
        } else {
            $validated = $request->validate([
                'name' => 'required|string',
                'organization' => 'required|string|unique:tenants,name',
                'email' => 'required|string|email|unique:users,email',
                'password' => [
                    'required',
                    'string',
                    'min:' . config('user_settings.validations.password.min'),
                    'confirmed',
                    'regex:' . config('user_settings.validations.password.regex'),
                ],
                'terms' => 'required|accepted',
                'country' => [
                    'required',
                    'string',
                    Rule::in($this->getCountriesArray()),
                ],
                'g-recaptcha-response' => 'recaptcha',
            ]);
        }

        $tenant_data = Arr::only($validated, ['organization', 'country']);

        $tenant_data['name'] = $tenant_data['organization'];
        $tenant_data['slug'] = Str::slug($tenant_data['organization']);

        // Prevent error with tenants with different names but same slug
        if (Tenant::where('slug', $tenant_data['slug'])->exists()) {
            $tenant_data['slug'] = $tenant_data['slug'] . '-' . Str::random(3);
        }

        Arr::forget($tenant_data, 'organization');
        $tenant = Tenant::create($tenant_data);

        $validated['tenant_id'] = $tenant->id;
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create(Arr::only($validated, ['name', 'email', 'password', 'tenant_id']));

        //Send user verification email
        $user->sendEmailVerificationNotification();

        $user->notify(new WelcomeMail($user));

        return $this->sendResponse([], 'Usuario registrado con éxito.');
    }
}
