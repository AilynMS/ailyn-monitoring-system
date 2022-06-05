<?php

namespace App\Http\Controllers\API\Tenant\Verifications;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Resources\Collections\TCPVerificationCollection;
use App\Http\Resources\Models\TCPVerification as TCPVerificationResource;
use App\Models\Verifications\TCPVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TrayLabs\InfluxDB\Facades\InfluxDB;

class TCPVerificationController extends Controller
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
            new TCPVerificationCollection(
                TCPVerification::query()->orderByDesc('updated_at')->paginate(15)
            ),
            'Verificaciones tcp encontradas'
        );
    }

    /**
     * Validates the input to create/update a resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  null|\App\Models\Verifications\TCPVerification  $tcpVerification
     *
     * @return array
     */
    protected function validateInput($request, $tcpVerification = null)
    {
        return $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'ipv6' => 'boolean',
            'type' => 'boolean',
            'target' => 'required|string',
            'port' => 'nullable|integer',
            'response_codes' => 'nullable|array',
            'response_codes.*' => 'string',
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
        $validated['token'] = 'tcp_udp_' . Str::random($token_length);

        $tcp_verification = TCPVerification::create($validated);

        return $this->sendResponse([
            'TCP_verification' => new TCPVerificationResource($tcp_verification),
        ], 'Verificación creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\TCPVerification  $tcpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($tenant, TCPVerification $tcpVerification)
    {
        return $this->sendResponse([
            'TCP_verification' => new TCPVerificationResource($tcpVerification),
        ], 'Verificación encontrada');
    }

    /**
     * Run the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\TCPVerification  $tcpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function runVerification($tenant, TCPVerification $tcpVerification)
    {
        if ($tcpVerification->isDisabled()) {
            return $this->sendError('Solo se pueden ejecutar verificaciones activas.', [], 500);
        }

        $success = $tcpVerification->runJob();

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
     * @param  \App\Models\Verifications\TCPVerification  $tcpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerificationData(Request $request, $tenant, TCPVerification $tcpVerification)
    {
        $start_date = Carbon::parse($request->get('start_date', now()->startOfDay()));
        $end_date = Carbon::parse($request->get('end_date', now()->endOfDay()));

        $result = InfluxDB::getQueryBuilder()
            ->select('time, status, responseText, "duration"')
            ->from($tcpVerification->getMeasurementName())
            ->where(["uid = '{$tcpVerification->token}'", "time > '{$start_date}'", "time <= '{$end_date}'"])
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
     * @param  \App\Models\Verifications\TCPVerification  $tcpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,  $tenant, TCPVerification $tcpVerification)
    {
        $validated = $this->validateInput($request);

        $tcpVerification->update($validated);

        return $this->sendResponse([
            'TCP_verification' => new TCPVerificationResource($tcpVerification),
        ], 'Verificación actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\TCPVerification  $tcpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($tenant, TCPVerification $tcpVerification)
    {
        if (!$tcpVerification->isDeleteable()) {
            return $this->sendError('No se puede borrar esta verificación', [], 500);
        }

        $tcpVerification->delete();

        return $this->sendResponse([], 'Verificación borrada con éxito');
    }
}
