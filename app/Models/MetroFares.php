<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\RatesAndFaresService;

// Супер условная модель сразу для Валюты и Тарифов. Служит лишь примером т.к. не храним реальных данных
class MetroFares extends Model
{
    private const DYNAMIC_PRICES = false;

    public const DEFAULT_RATES = [
        'USDT' => 1.005,
        'ETH' => 3066.33,
        'BTC' => 57929
    ];

    public function getCurrencyRate($currency)
    {
        if (self::DYNAMIC_PRICES) { /* Пример с вариантом конфига  */
            return RatesAndFaresService::fetchCurrencyRatesFromApi();
        }
        return self::DEFAULT_RATES[$currency];
    }

    public function getFares($currency)
    {
        return RatesAndFaresService::fetchFaresFromApi($currency);

        /* Можно создать Репозиторий, в данном случае использую сразу Сервис */
    }
}
