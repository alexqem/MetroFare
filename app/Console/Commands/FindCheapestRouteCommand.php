<?php

namespace App\Console\Commands;

use App\Services\MetroService;
use Illuminate\Console\Command;

class FindCheapestRouteCommand extends Command
{
    protected $signature = 'metro:find-cheapest-route';
    protected $description = 'Find the cheapest route between two metro stations';

    private MetroService $metroService;

    public function __construct(MetroService $metroService)
    {
        parent::__construct();
        $this->metroService = $metroService;
    }

    public function handle()
    {
        $start = $this->ask('Enter Start Platform ID:');
        $end = $this->ask('Enter Finish Platform ID:');

        $currencies = $this->choice(
            'Select currencies (multiple choice, comma-separated):',
            ['USDT', 'ETH', 'BTC'],
            null,
            null,
            true
        );

        $result = $this->metroService->findCheapestRoute($start, $end, $currencies);

        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
