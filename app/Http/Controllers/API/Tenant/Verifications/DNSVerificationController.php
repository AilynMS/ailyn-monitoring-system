<?php

namespace App\Http\Controllers\API\Tenant\Verifications;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Resources\Collections\DNSVerificationCollection;
use App\Http\Resources\Models\DNSVerification as DNSVerificationResource;
use App\Models\Verifications\DNSVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TrayLabs\InfluxDB\Facades\InfluxDB;

class DNSVerificationController extends Controller
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
            new DNSVerificationCollection(
                DNSVerification::query()->orderByDesc('updated_at')->paginate(15)
            ),
            'Verificaciones de dns encontradas'
        );
    }

    /**
     * Validates the input to create/update a resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  null|\App\Models\Verifications\DNSVerification  $dnsVerification
     *
     * @return array
     */
    protected function validateInput($request, $dnsVerification = null)
    {
        return $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'ipv6' => 'boolean',
            'target' => 'required|string',
            'expected_response' => 'required|string',
            'interval' => 'integer|min:1',
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
        $validated['token'] = 'dns_' . Str::random($token_length);

        $dns_verification = DNSVerification::create($validated);

        return $this->sendResponse([
            'dns_verification' => new DNSVerificationResource($dns_verification),
        ], 'Verificación creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\DNSVerification  $dnsVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($tenant, DNSVerification $dnsVerification)
    {
        return $this->sendResponse([
            'dns_verification' => new DNSVerificationResource($dnsVerification),
        ], 'Verificación encontrada');
    }

    /**
     * Run the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\DNSVerification  $dnsVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function runVerification($tenant, DNSVerification $dnsVerification)
    {
        if ($dnsVerification->isDisabled()) {
            return $this->sendError('Solo se pueden ejecutar verificaciones activas.', [], 500);
        }

        $success = $dnsVerification->runJob();

        if (! $success) {
            return $this->sendError('Algo ha salido mal, intentalo de nuevo.', [], 500);
        }

        return $this->sendResponse([], 'Verificación ejecutada con éxito');
    }

    /**
     * Get the specified resource data.
     *
     * @psalm-suppress all
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $tenant
     * @param  \App\Models\Verifications\DNSVerification  $dnsVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerificationData(Request $request, $tenant, DNSVerification $dnsVerification)
    {
        $start_date = Carbon::parse($request->get('start_date', now()->startOfDay()));
        $end_date = Carbon::parse($request->get('end_date', now()->endOfDay()));

        $result = InfluxDB::getQueryBuilder()
            ->select('time, a_record, "duration"')
            ->from($dnsVerification->getMeasurementName())
            ->where(["uid = '{$dnsVerification->token}'", "time > '{$start_date}'", "time <= '{$end_date}'"])
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
     * @param  \App\Models\Verifications\DNSVerification  $dnsVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,  $tenant, DNSVerification $dnsVerification)
    {
        $validated = $this->validateInput($request);

        $dnsVerification->update($validated);

        return $this->sendResponse([
            'dns_verification' => new DNSVerificationResource($dnsVerification),
        ], 'Verificación actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\DNSVerification  $dnsVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($tenant, DNSVerification $dnsVerification)
    {
        if (! $dnsVerification->isDeleteable()) {
            return $this->sendError('No se puede borrar esta verificación', [], 500);
        }

        $dnsVerification->delete();

        return $this->sendResponse([], 'Verificación borrada con éxito');
    }
}
