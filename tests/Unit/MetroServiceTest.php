<?php

namespace Tests\Unit;

use App\Models\MetroFares;
use App\Services\MetroService;
use PHPUnit\Framework\TestCase;

class MetroServiceTest extends TestCase
{
    private MetroService $metroService;
    private MetroFares $metroFares;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metroService = new MetroService();
        $this->metroFares = new MetroFares();

        // Mock the FARES constant in MetroService for testing
        $reflection = new \ReflectionClass($this->metroService);
        $reflection_property = $reflection->getProperty('FARES');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->metroService, [
            'USDT' => [
                [INF, 1, 2, INF],
                [1, INF, 1, 2],
                [2, 1, INF, 1],
                [INF, 2, 1, INF]
            ],
            'ETH' => [
                [INF, 0.0003, 0.0006, INF],
                [0.0003, INF, 0.0003, 0.0006],
                [0.0006, 0.0003, INF, 0.0003],
                [INF, 0.0006, 0.0003, INF]
            ],
            'BTC' => [
                [INF, 0.00002, 0.00004, INF],
                [0.00002, INF, 0.00002, 0.00004],
                [0.00004, 0.00002, INF, 0.00002],
                [INF, 0.00004, 0.00002, INF]
            ]
        ]);
    }

    public function testFindCheapestRouteSuccessful()
    {
        $result = $this->metroService->findCheapestRoute($this->metroFares, 0, 3, ['USDT', 'ETH', 'BTC']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('tickets', $result);
        $this->assertArrayHasKey('totalCostUSD', $result);

        $this->assertEquals([0, 1, 3], $result['path']);
        $this->assertCount(2, $result['tickets']);
        $this->assertEqualsWithDelta(3, $result['totalCostUSD'], 0.01);
    }

    public function testFindCheapestRouteNoPath()
    {
        $reflection = new \ReflectionClass($this->metroService);
        $reflection_property = $reflection->getProperty('FARES');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->metroService, [
            'USDT' => [
                [INF, 1, INF, INF],
                [1, INF, 1, INF],
                [INF, 1, INF, 1],
                [INF, INF, 1, INF]
            ]
        ]);

        $result = $this->metroService->findCheapestRoute($this->metroFares, 0, 3, ['USDT']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Path not found', $result['error']);
    }
}
