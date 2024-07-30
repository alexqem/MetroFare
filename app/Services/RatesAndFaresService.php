<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RatesAndFaresService
{
    public static function fetchCurrencyRatesFromApi()
    {
        // В реальном приложении здесь был бы настоящий API-запрос
        $response = Http::fake([
            'api.example.com/currency-rates' => Http::response([
                'USDT' => 1.005,
                'ETH' => 3066.33,
                'BTC' => 57929
            ], 200)
        ])->get('api.example.com/currency-rates');

        return $response->json();
    }

    public static function fetchFaresFromApi($currency): array
    {
        $fares = self::prepareFaresForJson(self::FARES[$currency]);

        Http::fake([
            "api.example.com/fares/{$currency}" => Http::response($fares, 200, ['Content-Type' => 'application/json'])
        ]);
        $response = Http::get("api.example.com/fares/{$currency}");

        return self::FARES[$currency];
    }

    private const FARES = [
        'USDT' => [
            [INF, 0.69652, 1.39254, INF, INF, 2.08557],
            [0.69652, INF, 0.69652, 1.39254, INF, INF],
            [1.39254, 0.69652, INF, 0.69652, 1.39254, INF],
            [INF, 1.39254, 0.69652, INF, 1.39254, 0.69652],
            [INF, INF, 1.39254, 1.39254, INF, 0.69652],
            [2.08557, INF, INF, 0.69652, 0.69652, INF],
        ],
        'ETH' => [
            [INF, 0.00022828, 0.00045656, INF, INF, 0.00068484],
            [0.00022828, INF, 0.00022828, 0.00045656, INF, INF],
            [0.00045656, 0.00022828, INF, 0.00022828, 0.00045656, INF],
            [INF, 0.00045656, 0.00022828, INF, 0.00045656, 0.00022828],
            [INF, INF, 0.00045656, 0.00045656, INF, 0.00022828],
            [0.00068484, INF, INF, 0.00022828, 0.00022828, INF],
        ],
        'BTC' => [
            [INF, 0.00001209, 0.00002417, INF, INF, 0.00003626],
            [0.00001209, INF, 0.00001209, 0.00002417, INF, INF],
            [0.00002417, 0.00001209, INF, 0.00001209, 0.00002417, INF],
            [INF, 0.00002417, 0.00001209, INF, 0.00002417, 0.00001209],
            [INF, INF, 0.00002417, 0.00002417, INF, 0.00001209],
            [0.00003626, INF, INF, 0.00001209, 0.00001209, INF],
        ],
    ];

    // Конверт INF в строку иначе Http::response не съедает
    private static function prepareFaresForJson(array $fares): array
    {
        return array_map(function ($row) {
            return array_map(function ($value) {
                return is_infinite($value) ? 'INF' : $value;
            }, $row);
        }, $fares);
    }
}



