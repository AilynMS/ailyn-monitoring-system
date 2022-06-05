<?php

namespace App\Http\Controllers\API\Tenant\Verifications;

use App\Http\Controllers\API\BaseController as Controller;
use App\Http\Resources\Collections\ICMPVerificationCollection;
use App\Http\Resources\Models\ICMPVerification as ICMPVerificationResource;
use App\Models\Verifications\ICMPVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TrayLabs\InfluxDB\Facades\InfluxDB;

class ICMPVerificationController extends Controller
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
            new ICMPVerificationCollection(
                ICMPVerification::query()->orderByDesc('updated_at')->paginate(15)
            ),
            'Verificaciones de icmp encontradas'
        );
    }

    /**
     * Validates the input to create/update a resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  null|\App\Models\Verifications\ICMPVerification  $icmpVerification
     *
     * @return array
     */
    protected function validateInput($request, $icmpVerification = null)
    {
        return $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'ipv6' => 'boolean',
            'target' => 'required|string',
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
        $validated['token'] = 'icmp_' . Str::random($token_length);

        $icmp_verification = ICMPVerification::create($validated);

        return $this->sendResponse([
            'icmp_verification' => new ICMPVerificationResource($icmp_verification),
        ], 'Verificación creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\ICMPVerification  $icmpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($tenant, ICMPVerification $icmpVerification)
    {
        return $this->sendResponse([
            'icmp_verification' => new ICMPVerificationResource($icmpVerification),
        ], 'Verificación encontrada');
    }

    /**
     * Run the specified resource.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\ICMPVerification  $icmpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function runVerification($tenant, ICMPVerification $icmpVerification)
    {
        if ($icmpVerification->isDisabled()) {
            return $this->sendError('Solo se pueden ejecutar verificaciones activas.', [], 500);
        }

        $success = $icmpVerification->runJob();

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
     * @param  \App\Models\Verifications\ICMPVerification  $icmpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerificationData(Request $request, $tenant, ICMPVerification $icmpVerification)
    {
        $start_date = Carbon::parse($request->get('start_date', now()->startOfDay()));
        $end_date = Carbon::parse($request->get('end_date', now()->endOfDay()));

        $result = InfluxDB::getQueryBuilder()
            ->select('time, is_alive, "duration"')
            ->from($icmpVerification->getMeasurementName())
            ->where(["uid = '{$icmpVerification->token}'", "time > '{$start_date}'", "time <= '{$end_date}'"])
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
     * @param  \App\Models\Verifications\ICMPVerification  $icmpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,  $tenant, ICMPVerification $icmpVerification)
    {
        $validated = $this->validateInput($request);

        $icmpVerification->update($validated);

        return $this->sendResponse([
            'icmp_verification' => new ICMPVerificationResource($icmpVerification),
        ], 'Verificación actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $tenant
     * @param  \App\Models\Verifications\ICMPVerification  $icmpVerification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($tenant, ICMPVerification $icmpVerification)
    {
        if (! $icmpVerification->isDeleteable()) {
            return $this->sendError('No se puede borrar esta verificación', [], 500);
        }

        $icmpVerification->delete();

        return $this->sendResponse([], 'Verificación borrada con éxito');
    }
}
