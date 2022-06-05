<?php

namespace App\Http\Controllers\API\Tenant\Verifications;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Resources\Collections\SNMPVerificationCollection;
use App\Http\Resources\Models\SNMPVerification as SNMPVerificationResource;
use App\Models\Verifications\SNMPVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TrayLabs\InfluxDB\Facades\InfluxDB;

class SNMPVerificationController extends Controller
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
            new SNMPVerificationCollection(
                SNMPVerification::query()->orderByDesc('updated_at')->paginate(15)
            ),
            'Verificaciones snmp encontradas'
        );
    }

    /**
     * Validates the input to create/update a resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  null|\App\Models\Verifications\SNMPVerification  $SNMPVerification
     *
     * @return array
     */
    protected function validateInput($request, $snmpVerification = null)
    {
        return $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'ipv6' => 'boolean',
            'version' => 'string',
            'target' => 'required|string',
            'data_type' => 'required|string',
            'oid' => 'required|string',
            'community' => 'required|string',
            'port' => 'nullable|integer',
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
        $validated['token'] = 'snmp_' . Str::random($token_length);

        $snmp_verification = SNMPVerification::create($validated);

        return $this->sendResponse([
            'snmp_verification' => new SNMPVerificationResource($snmp_verification),
        ], 'Verificación creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\SNMPVerification  $SNMPVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($tenant, SNMPVerification $snmpVerification)
    {
        return $this->sendResponse([
            'snmp_verification' => new SNMPVerificationResource($snmpVerification),
        ], 'Verificación encontrada');
    }

    /**
     * Run the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\SNMPVerification  $SNMPVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function runVerification($tenant, SNMPVerification $snmpVerification)
    {
        if ($snmpVerification->isDisabled()) {
            return $this->sendError('Solo se pueden ejecutar verificaciones activas.', [], 500);
        }

        $success = $snmpVerification->runJob();

        if (!$success) {
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
     * @param  \App\Models\Verifications\SNMPVerification  $SNMPVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerificationData(Request $request, $tenant, SNMPVerification $snmpVerification)
    {
        $start_date = Carbon::parse($request->get('start_date', now()->startOfDay()));
        $end_date = Carbon::parse($request->get('end_date', now()->endOfDay()));

        $result = InfluxDB::getQueryBuilder()
            ->select('time, value')
            ->from($snmpVerification->getMeasurementName())
            ->where(["uid = '{$snmpVerification->token}'", "time > '{$start_date}'", "time <= '{$end_date}'"])
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
     * @param  \App\Models\Verifications\SNMPVerification  $SNMPVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,  $tenant, SNMPVerification $snmpVerification)
    {
        $validated = $this->validateInput($request);

        $snmpVerification->update($validated);

        return $this->sendResponse([
            'snmp_verification' => new SNMPVerificationResource($snmpVerification),
        ], 'Verificación actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\SNMPVerification  $SNMPVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($tenant, SNMPVerification $snmpVerification)
    {
        if (!$snmpVerification->isDeleteable()) {
            return $this->sendError('No se puede borrar esta verificación', [], 500);
        }

        $snmpVerification->delete();

        return $this->sendResponse([], 'Verificación borrada con éxito');
    }
}
