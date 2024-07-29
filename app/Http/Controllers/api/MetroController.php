<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheapestRouteRequest;
use App\Http\Resources\CheapestRouteResource;
use App\Models\MetroFares;
use Illuminate\Http\Request;
use App\Services\MetroService;

class MetroController extends Controller
{
    private MetroService $metroService;

    public function __construct(MetroService $metroService)
    {
        $this->metroService = $metroService;
    }

    /**
     * @param CheapestRouteRequest $request
     * @return CheapestRouteResource
     */
    public function getCheapestRoute(CheapestRouteRequest $request): CheapestRouteResource
    {
        $begin = (int)$request['begin'];
        $end = (int)$request['end'];
        $currencies = $request['currencies'];

        $cheapestRoute = $this->metroService->findCheapestRoute($begin, $end, $currencies);

        if (isset($cheapestRoute['error'])) {
            $cheapestRoute['error'] = "Route from {$begin} to {$end} not found";
        }

        return new CheapestRouteResource($cheapestRoute);
    }
}
