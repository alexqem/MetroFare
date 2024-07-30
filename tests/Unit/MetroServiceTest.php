<?php

namespace Tests\Unit;

use App\Services\MetroService;
use App\Models\MetroFares;
use PHPUnit\Framework\TestCase;

class MetroServiceTest extends TestCase
{
    protected MetroFares $metroFares;
    protected MetroService $metroService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->metroFares = $this->createMock(MetroFares::class);
        $this->metroService = new MetroService($this->metroFares);
    }

    public function testValidPath()
    {
        // Создаем фиктивные данные о тарифах для теста
        $fakeFares = [
            [INF, 1, INF, INF],
            [1, INF, 2, INF],
            [INF, 2, INF, 3],
            [INF, INF, 3, INF],
        ];

        // Задаем поведение мока для метода getFares
        $this->metroFares->method('getFares')->willReturn($fakeFares);
        $this->metroFares->method('getCurrencyRate')->willReturn(1);

        // Вызываем метод findCheapestRoute с тестовыми данными
        $result = $this->metroService->findCheapestRoute(0, 3, ['USDT']);

        // Проверяем результат
        $this->assertArrayHasKey('path', $result);
        $this->assertEquals([0, 1, 2, 3], $result['path']);
        $this->assertEquals('6.00', $result['totalCostUSD']);
    }

    public function testNoPath()
    {
        $fakeFares = [
            [INF, 1, INF, INF],
            [1, INF, INF, INF],
            [INF, INF, INF, INF],
            [INF, INF, INF, INF],
        ];

        $this->metroFares->method('getFares')->willReturn($fakeFares);

        $result = $this->metroService->findCheapestRoute(0, 3, ['USDT']);

        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Path not found', $result['error']);
    }

    public function testInsufficientFareData()
    {
        $fakeFares = [
            [INF, null, INF, INF],
            [null, INF, INF, INF],
            [INF, INF, INF, null],
            [INF, INF, null, INF],
        ];

        $this->metroFares->method('getFares')->willReturn($fakeFares);

        $result = $this->metroService->findCheapestRoute(0, 3, ['USDT']);

        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Path not found', $result['error']);
    }
}
