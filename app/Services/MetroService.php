<?php

namespace App\Services;

use App\Models\MetroFares;
use Illuminate\Support\Facades\Http;

class MetroService
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

    public static function fetchFaresFromApi($currency)
    {
        $response = Http::fake([
            "api.example.com/fares/{$currency}" => Http::response(self::FARES[$currency], 200)
        ])->get("api.example.com/fares/{$currency}");

        return $response->json();

        /* Можно накинуть кэш с обновлением в N минут. try catch, Лог Sentry, Graylog */
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

    public function findCheapestRoute(int $begin, int $end, $currencies): array
    {
        $numStations = count(self::FARES['USDT']);
        $dist = array_fill(0, $numStations, array_fill(0, $numStations, INF));
        $next = array_fill(0, $numStations, array_fill(0, $numStations, -1));
        $currencyUsed = array_fill(0, $numStations, array_fill(0, $numStations, ''));

        // Определяем расстояние и пересадки
        foreach ($currencies as $currency) {
            for ($i = 0; $i < $numStations; $i++) {
                for ($j = 0; $j < $numStations; $j++) {
                    $fare = self::FARES[$currency][$i][$j];
                    if ($fare < $dist[$i][$j]) {
                        $dist[$i][$j] = $fare;
                        $next[$i][$j] = $j;
                        $currencyUsed[$i][$j] = $currency;
                    }
                }
            }
        }

        // Алгоритм Флойда-Уоршелла
        for ($k = 0; $k < $numStations; $k++) {
            for ($i = 0; $i < $numStations; $i++) {
                for ($j = 0; $j < $numStations; $j++) {
                    if ($dist[$i][$k] + $dist[$k][$j] < $dist[$i][$j]) {
                        $dist[$i][$j] = $dist[$i][$k] + $dist[$k][$j];
                        $next[$i][$j] = $next[$i][$k];
                        $currencyUsed[$i][$j] = $currencyUsed[$i][$k];
                    }
                }
            }
        }

        // Пересобираем путь
        $path = [];
        $tickets = [];
        $totalCost = 0;
        $current = $begin;

        while ($current != $end) {
            $path[] = $current;
            $nextStation = $next[$current][$end] ?? null;

            if (!$nextStation) {
                return ['error' => 'Path not found'];
            }

            $currency = $currencyUsed[$current][$nextStation];
            $cost = self::FARES[$currency][$current][$nextStation];

            $tickets[] = [
                'from' => $current,
                'to' => $nextStation,
                'currency' => $currency,
                'cost' => number_format($cost, 10, '.', '')
            ];

            $totalCost += $cost * MetroFares::DEFAULT_RATES[$currency];
            $current = $nextStation;
        }

        $path[] = $end;
        $totalCost = number_format($totalCost, 2, '.', '');

        return [
            'path' => $path,
            'tickets' => $tickets,
            'totalCostUSD' => $totalCost
        ];
    }

}



