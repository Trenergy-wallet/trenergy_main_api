<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\DTO;

use Apd\Trenergy\DTO\ArrayDTO;
use Apd\Trenergy\DTO\BaseDTO;
use Apd\Trenergy\DTO\Consumers\ConsumerSummaryDTO;
use Apd\Trenergy\DTO\Consumers\PeriodPricesSunDTO;
use Apd\Trenergy\Tests\TestCase;
use Faker\Provider\Base;

class BaseDTOTest extends TestCase
{
    /** @test */
    public function it_handles_nested_dtos()
    {
        $periodPricesSunData = [
            "15" => 76,
            "60" => 100,
            "480" => 127,
            "1440" => 127
        ];

        $periodPricesSunDTO = new PeriodPricesSunDTO($periodPricesSunData);

        $consumerSummaryData = [
            'balance' => 100.0,
            'credit_limit' => 10.0,
            'total_count' => 3,
            'total_energy_consumption' => 12.2,
            'total_received_energy' => 32.2,
            'active_count' => 7,
            'active_energy_consumption' => 7.7,
            'normal_energy_unit_price' => 12.2,
            'trenergy_energy_unit_price' => 32.2,
            'aml_price_usd' => 12.2,
            'daily_expenses_avg' => 43.3,
            'period_prices_sun' => $periodPricesSunDTO
        ];

        $dto = new ConsumerSummaryDTO($consumerSummaryData);

        // Проверка основных свойств
        $this->assertInstanceOf(\Apd\Trenergy\DTO\Consumers\ConsumerSummaryDTO::class, $dto);
        $this->assertEquals(100.0, $dto->balance);
        $this->assertEquals(10.0, $dto->credit_limit);
        $this->assertEquals(3, $dto->total_count);
        $this->assertEquals(12.2, $dto->total_energy_consumption);
        $this->assertEquals(32.2, $dto->total_received_energy);
        $this->assertEquals(7, $dto->active_count);
        $this->assertEquals(7.7, $dto->active_energy_consumption);
        $this->assertEquals(12.2, $dto->normal_energy_unit_price);
        $this->assertEquals(32.2, $dto->trenergy_energy_unit_price);
        $this->assertEquals(12.2, $dto->aml_price_usd);
        $this->assertEquals(43.3, $dto->daily_expenses_avg);

// Проверка вложенного PeriodPricesSunDTO
        $this->assertInstanceOf(\Apd\Trenergy\DTO\Consumers\PeriodPricesSunDTO::class, $dto->period_prices_sun);

// Проверка содержимого period_prices_sun
        $expectedPrices = [
            15 => 76,
            60 => 100,
            480 => 127,
            1440 => 127
        ];
        $this->assertEquals($expectedPrices, $dto->period_prices_sun->paymentPeriods);

// Альтернативная проверка period_prices_sun с проверкой типов
        $this->assertIsArray($dto->period_prices_sun->paymentPeriods);
        $this->assertArrayHasKey(15, $dto->period_prices_sun->paymentPeriods);
        $this->assertEquals(76, $dto->period_prices_sun->paymentPeriods[15]);
        $this->assertArrayHasKey(1440, $dto->period_prices_sun->paymentPeriods);
        $this->assertEquals(127, $dto->period_prices_sun->paymentPeriods[1440]);

// Проверка количества элементов в period_prices_sun
        $this->assertCount(4, $dto->period_prices_sun->paymentPeriods);
    }

    /** @test */
    public function it_cast_to()
    {
        $dto = new BaseDTO([]);
        $class = new \ReflectionClass($dto);
        $method = $class->getMethod('castToType');

        $result = $method->invokeArgs($dto, ['123', 'string']);
        $this->assertSame('123', $result);

        $result = $method->invokeArgs($dto, ['123', 'int']);
        $this->assertSame(123, $result);

        $result = $method->invokeArgs($dto, ['123', 'float']);
        $this->assertSame(123.0, $result);

        $result = $method->invokeArgs($dto, ['123', 'bool']);
        $this->assertSame(true, $result);

        $result = $method->invokeArgs($dto, [[], 'array']);
        $this->assertSame([], $result);
    }

    /** @test */
    public function it_uses_casts_configuration()
    {
        $dto = new class(['test_int' => '123']) extends BaseDTO {
            public $test_int;
            protected $casts = ['test_int' => 'int'];

            // Делаем метод публичным для тестирования
            public function testCastValue($key, $value) {
                return $this->castValue($key, $value);
            }
        };

        $result = $dto->testCastValue('test_int', '456');
        $this->assertSame(456, $result);
    }

    /** @test */
    public function it_dto_to_array()
    {
        $dto = new class(['name' => 'Test']) extends BaseDTO {
            public string $name;
        };

        $result = $dto->toArray();

        $this->assertIsArray($result);
        $this->assertEquals(['name' => 'Test'], $result);
    }

    /** @test */
    public function it_creates_array_dto_for_assoc_arrays()
    {
        $assocArray = [
            'id' => 1,
            'name' => 'Test Item',
            'metadata' => ['key' => 'value']
        ];

        $dto = new class([]) extends BaseDTO {
            public function testCreateNestedDto($key, $data) {
                return $this->createNestedDto($key, $data);
            }
        };

        $result = $dto->testCreateNestedDto('item', $assocArray);

        $this->assertInstanceOf(ArrayDTO::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Test Item', $result->name);
        $this->assertEquals('value', $result->metadata['key']);
    }

    /** @test */
    public function it_creates_nested_dto_when_class_defined()
    {
        $testData = ['name' => 'Test User', 'email' => 'test@example.com'];

        $dto = new class(['user' => $testData]) extends BaseDTO {
            protected array $nestedDtos = [
                'user' => ArrayDTO::class
            ];

            public function testCreateNestedDto($key, $data) {
                return $this->createNestedDto($key, $data);
            }
        };

        $result = $dto->testCreateNestedDto('user', $testData);

        $this->assertInstanceOf(ArrayDTO::class, $result);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
    }
}
