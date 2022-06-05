<?php

namespace App\Http\Controllers\API\Tenant\Verifications;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Resources\Collections\WebVerificationCollection;
use App\Http\Resources\Models\WebVerification as WebVerificationResource;
use App\Models\Verifications\WebVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TrayLabs\InfluxDB\Facades\InfluxDB;

class WebVerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        return $this->sendCollectionResource(
            new WebVerificationCollection(
                WebVerification::query()->orderByDesc('updated_at')->paginate(15)
            ),
            'Verificaciones web encontradas'
        );
    }

    /**
     * Validates the input to create/update a resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  null|\App\Models\Verifications\WebVerification  $webVerification
     *
     * @return array
     */
    protected function validateInput($request, $webVerification = null)
    {
        return $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'ipv6' => 'boolean',
            'https' => 'boolean',
            'target' => 'required|string',
            'path' => 'nullable|string',
            'port' => 'nullable|integer',
            'response_codes' => 'nullable|array',
            'response_codes.*' => 'integer',
            'interval' => 'integer|min:1',
            'headers' => 'nullable|array',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $this->validateInput($request);

        $token_length = config('verification_settings.token_length');
        $validated['token'] = 'web_' . Str::random($token_length);

        $web_verification = WebVerification::create($validated);

        return $this->sendResponse([
            'web_verification' => new WebVerificationResource($web_verification),
        ], 'Verificación creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\WebVerification  $webVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($tenant, WebVerification $webVerification)
    {
        return $this->sendResponse([
            'web_verification' => new WebVerificationResource($webVerification),
        ], 'Verificación encontrada');
    }

    /**
     * Run the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\WebVerification  $webVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function runVerification($tenant, WebVerification $webVerification)
    {
        if ($webVerification->isDisabled()) {
            return $this->sendError('Solo se pueden ejecutar verificaciones activas.', [], 500);
        }

        $success = $webVerification->runJob();

        if (! $success) {
            return $this->sendError('Algo ha salido mal, intentalo de nuevo.', [], 500);
        }

        return $this->sendResponse([], 'Verificación ejecutada con exito');
    }

    /**
     * Get the specified resource data.
     *
     * @psalm-suppress all
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $tenant
     * @param  \App\Models\Verifications\WebVerification  $webVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerificationData(Request $request, $tenant, WebVerification $webVerification)
    {
        $start_date = Carbon::parse($request->get('start_date', now()->startOfDay()));
        $end_date = Carbon::parse($request->get('end_date', now()->endOfDay()));

        $result = InfluxDB::getQueryBuilder()
            ->select('time, status, "duration"')
            ->from($webVerification->getMeasurementName())
            ->where(["uid = '{$webVerification->token}'", "time > '{$start_date}'", "time <= '{$end_date}'"])
            // ->limit(100)
            ->orderBy('time', 'DESC')
            ->getResultSet()
            ->getPoints();

        return $this->sendResponse([
            'result' => $result,
        ], 'Datos encontrados');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $tenant
     * @param  \App\Models\Verifications\WebVerification  $webVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,  $tenant, WebVerification $webVerification)
    {
        $validated = $this->validateInput($request);

        $webVerification->update($validated);

        return $this->sendResponse([
            'web_verification' => new WebVerificationResource($webVerification),
        ], 'Verificación actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\WebVerification  $webVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($tenant, WebVerification $webVerification)
    {
        if (! $webVerification->isDeleteable()) {
            return $this->sendError('No se puede borrar esta verificación', [], 500);
        }

        $webVerification->delete();

        return $this->sendResponse([], 'Verificación borrada con éxito');
    }
}
