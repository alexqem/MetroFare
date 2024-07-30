<?php

namespace App\Console\Commands;

use App\Services\MetroService;
use Illuminate\Console\Command;

class FindCheapestRouteCommand extends Command
{
    protected $signature = 'metro:find-cheapest-route';
    protected $description = 'Найти самый дешевый путь между платформами';

    private MetroService $metroService;

    public function __construct(MetroService $metroService)
    {
        parent::__construct();
        $this->metroService = $metroService;
    }

    public function handle()
    {
        $start = $this->ask('Введите ID платформы отправления');
        $end = $this->ask('Введите ID платформы назначения');

        $currencies = $this->choice(
            'Выберете валюту оплаты (множество, через запятую)',
            ['USDT', 'ETH', 'BTC'],
            null,
            null,
            true
        );

        $result = $this->metroService->findCheapestRoute($start, $end, $currencies);

        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
