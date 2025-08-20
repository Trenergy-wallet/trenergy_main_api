<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\DTO;

use Apd\Trenergy\DTO\Consumers\ConsumerDTO;
use Apd\Trenergy\DTO\Consumers\OrderDTO;
use Apd\Trenergy\DTO\Consumers\BoostOrderDTO;
use Apd\Trenergy\Tests\TestCase;

class ConsumerDTOTest extends TestCase
{
    /** @test */
    public function it_creates_consumer_dto_from_array()
    {
        $data = [
            'id' => 123,
            'name' => 'Test Consumer',
            'address' => '0x1234567890abcdef',
            'resource_amount' => 100,
            'creation_type' => 1,
            'consumption_type' => 2,
            'payment_period' => 15,
            'auto_renewal' => true,
            'resource_consumptions' => [['date' => '2023-01-01', 'amount' => 10]],
            'is_active' => true,
            'order' => ['status' => 1, 'completion_percentage' => 50],
            'boostOrder' => ['status' => 2, 'completion_percentage' => 75],
            'webhook_url' => 'https://example.com/webhook',
            'created_at' => '2023-01-01 00:00:00',
            'updated_at' => '2023-01-02 00:00:00'
        ];

        $dto = new ConsumerDTO($data);

        // Проверка основных свойств
        $this->assertEquals(123, $dto->id);
        $this->assertEquals('Test Consumer', $dto->name);
        $this->assertEquals('0x1234567890abcdef', $dto->address);
        $this->assertEquals(100, $dto->resource_amount);
        $this->assertEquals(1, $dto->creation_type);
        $this->assertEquals(2, $dto->consumption_type);
        $this->assertEquals(15, $dto->payment_period);
        $this->assertTrue($dto->auto_renewal);
        $this->assertTrue($dto->is_active);
        $this->assertEquals('https://example.com/webhook', $dto->webhook_url);
        $this->assertEquals('2023-01-01 00:00:00', $dto->created_at);
        $this->assertEquals('2023-01-02 00:00:00', $dto->updated_at);

        // Проверка вложенных DTO
        $this->assertInstanceOf(OrderDTO::class, $dto->order);
        $this->assertEquals(1, $dto->order->status);
        $this->assertEquals(50, $dto->order->completion_percentage);

        $this->assertInstanceOf(BoostOrderDTO::class, $dto->boostOrder);
        $this->assertEquals(2, $dto->boostOrder->status);
        $this->assertEquals(75, $dto->boostOrder->completion_percentage);

        // Проверка массива
        $this->assertIsArray($dto->resource_consumptions);
        $this->assertEquals('2023-01-01', $dto->resource_consumptions[0]['date']);
        $this->assertEquals(10, $dto->resource_consumptions[0]['amount']);
    }

    /** @test */
    public function it_handles_nullable_fields()
    {
        $data = [
            'id' => 123,
            'name' => 'Test Consumer',
            'address' => '0x1234567890abcdef',
            'resource_amount' => 100,
            'creation_type' => 1,
            'consumption_type' => 2,
            'payment_period' => 15,
            'auto_renewal' => true,
            'resource_consumptions' => null,
            'is_active' => true,
            'order' => ['status' => 1],
            'boostOrder' => ['status' => 2],
            'webhook_url' => null,
            'created_at' => '2023-01-01',
            'updated_at' => '2023-01-02'
        ];

        $dto = new ConsumerDTO($data);

        $this->assertNull($dto->resource_consumptions);
        $this->assertNull($dto->webhook_url);
    }

    /** @test */
    public function it_properly_casts_values()
    {
        $data = [
            'id' => '123', // string to int
            'name' => 12345, // int to string
            'address' => '0x1234567890abcdef',
            'resource_amount' => '100', // string to int
            'creation_type' => '1', // string to int
            'consumption_type' => '2', // string to int
            'payment_period' => '15', // string to int
            'auto_renewal' => 1, // int to bool
            'resource_consumptions' => null,
            'is_active' => 'true', // string to bool
            'order' => ['status' => 1], // string to int inside nested DTO
            'boostOrder' => ['status' => 2], // string to int inside nested DTO
            'webhook_url' => null,
            'created_at' => 1672531200, // timestamp to string
            'updated_at' => 1672617600 // timestamp to string
        ];

        $dto = new ConsumerDTO($data);

        $this->assertIsInt($dto->id);
        $this->assertIsString($dto->name);
        $this->assertIsInt($dto->resource_amount);
        $this->assertIsInt($dto->creation_type);
        $this->assertIsInt($dto->consumption_type);
        $this->assertIsInt($dto->payment_period);
        $this->assertIsBool($dto->auto_renewal);
        $this->assertIsBool($dto->is_active);
        $this->assertIsString($dto->created_at);
        $this->assertIsString($dto->updated_at);
    }
}
