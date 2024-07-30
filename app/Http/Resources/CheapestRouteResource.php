<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheapestRouteResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        if (isset($this['error'])) {
            return [
                'route' => null,
                'error' => $this['error']
            ];
        }

        return [
            'route' => $this['path'],
            'total_cost_usd' => round($this['totalCostUSD'], 2),
            'tickets' => collect($this['tickets'])->map(function ($ticket) {
                return [
                    'from' => $ticket['from'],
                    'to' => $ticket['to'],
                    'cost' => $ticket['cost'],
                    'currency' => $ticket['currency'],
                ];
            }),
        ];
    }
}
