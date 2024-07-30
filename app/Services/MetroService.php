<?php

namespace App\Services;

use App\Models\MetroFares;

class MetroService
{
    private MetroFares $metroFares;

    public function __construct(MetroFares $metroFares)
    {
        $this->metroFares = $metroFares;
    }

    /**
     * Поиск кратчайшего пути между двумя станциями
     * Разбит по KISS, ниже есть nonKiss метод
     *
     * @param int $begin Начальная платформа
     * @param int $end Конечная платформа
     * @param array $currencies
     * @return array Ответ или массив с ошибкой
     */
    public function findCheapestRoute(int $begin, int $end, array $currencies): array
    {
        $fares = $this->getFaresForCurrencies($currencies);

        $numStations = count($fares['USDT']);
        $dist = $this->initializeMatrix($numStations, INF);
        $next = $this->initializeMatrix($numStations, -1);
        $currencyUsed = $this->initializeMatrix($numStations, '');

        $this->findInitialShortestPaths($currencies, $fares, $dist, $next, $currencyUsed);

        $this->applyFloydWarshall($numStations, $dist, $next, $currencyUsed);

        return $this->reconstructPath($begin, $end, $next, $currencyUsed, $fares);
    }

    /**
     * Получаем Тарифы на все пути
     *
     * @param array $currencies
     * @return array
     */
    private function getFaresForCurrencies(array $currencies): array
    {
        $fares = [];
        foreach ($currencies as $currency) {
            $fares[$currency] = $this->metroFares->getFares($currency);
        }
        return $fares;
    }

    /**
     * Задаем 2Д матрицу
     *
     * @param int $size
     * @param mixed $defaultValue
     * @return array
     */
    private function initializeMatrix(int $size, mixed $defaultValue): array
    {
        return array_fill(0, $size, array_fill(0, $size, $defaultValue));
    }

    /**
     * Ищем короткий путь для всх валют
     *
     * @param array $currencies
     * @param array $fares
     * @param array &$dist
     * @param array &$next
     * @param array &$currencyUsed
     */
    private function findInitialShortestPaths(array $currencies, array $fares, array &$dist, array &$next, array &$currencyUsed): void
    {
        foreach ($currencies as $currency) {
            for ($i = 0; $i < count($dist); $i++) {
                for ($j = 0; $j < count($dist); $j++) {
                    $fare = $fares[$currency][$i][$j];
                    if ($fare < $dist[$i][$j]) {
                        $dist[$i][$j] = $fare;
                        $next[$i][$j] = $j;
                        $currencyUsed[$i][$j] = $currency;
                    }
                }
            }
        }
    }

    /**
     * Алгоритм Флойда-Уоршелла
     *
     * @param int $numStations
     * @param array &$dist
     * @param array &$next
     * @param array &$currencyUsed
     */
    private function applyFloydWarshall(int $numStations, array &$dist, array &$next, array &$currencyUsed): void
    {
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
    }

    /**
     * Пересобираем путь с начальной до конечной
     *
     * @param int $begin
     * @param int $end
     * @param array $next
     * @param array $currencyUsed
     * @param array $fares
     * @return array
     */
    private function reconstructPath(int $begin, int $end, array $next, array $currencyUsed, array $fares): array
    {
        $path = [];
        $tickets = [];
        $totalCost = 0;
        $current = $begin;

        while ($current != $end) {
            $path[] = $current;
            $nextStation = $next[$current][$end] ?? null;

            if ($nextStation === null || $nextStation === -1) {
                return ['error' => 'Path not found'];
            }

            $currency = $currencyUsed[$current][$nextStation];
            $cost = $fares[$currency][$current][$nextStation];
            if ($cost === null) {
                return ['error' => 'Path not found'];
            }

            $tickets[] = [
                'from' => $current,
                'to' => $nextStation,
                'currency' => $currency,
                'cost' => number_format($cost, 10, '.', '')
            ];
            $totalCost += $cost * $this->metroFares->getCurrencyRate($currency);
            $current = $nextStation;
        }

        $path[] = $end;

        return [
            'path' => $path,
            'tickets' => $tickets,
            'totalCostUSD' => number_format($totalCost, 2, '.', '')
        ];
    }

    /**
     * @deprecated
     */
    private function findCheapestRouteNoneKISS(int $begin, int $end, $currencies): array
    {
        $fares = [];
        foreach ($currencies as $currency) {
            $fares[$currency] = $this->metroFares->getFares($currency);
        }

        $numStations = count($this->metroFares->getFares('USDT'));
        $dist = array_fill(0, $numStations, array_fill(0, $numStations, INF));
        $next = array_fill(0, $numStations, array_fill(0, $numStations, -1));
        $currencyUsed = array_fill(0, $numStations, array_fill(0, $numStations, ''));

        // Определяем расстояние и пересадки
        foreach ($currencies as $currency) {
            for ($i = 0; $i < $numStations; $i++) {
                for ($j = 0; $j < $numStations; $j++) {
                    $fare = $fares[$currency][$i][$j];
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
            $cost = $fares[$currency][$current][$nextStation];

            $tickets[] = [
                'from' => $current,
                'to' => $nextStation,
                'currency' => $currency,
                'cost' => number_format($cost, 10, '.', '')
            ];
            $totalCost += $cost *  $this->metroFares->getCurrencyRate($currency);
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



