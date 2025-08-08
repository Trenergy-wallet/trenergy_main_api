<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\DTO;

use Apd\Trenergy\DTO\ArrayDTO;
use PHPUnit\Framework\TestCase;

class ArrayDTOTest extends TestCase
{
    /** @test */
    public function it_stores_and_returns_array_data()
    {
        $testData = [
            'name' => 'Test',
            'value' => 123,
            'nested' => ['key' => 'value']
        ];

        $dto = new ArrayDTO($testData);

        // Проверка toArray()
        $this->assertEquals($testData, $dto->toArray());
    }

    /** @test */
    public function it_provides_property_style_access()
    {
        $data = [
            'id' => 1,
            'title' => 'Test Item',
            'is_active' => true
        ];

        $dto = new ArrayDTO($data);

        // Проверка __get()
        $this->assertEquals(1, $dto->id);
        $this->assertEquals('Test Item', $dto->title);
        $this->assertTrue($dto->is_active);
    }

    /** @test */
    public function it_returns_null_for_missing_keys()
    {
        $dto = new ArrayDTO(['existing' => 'value']);

        $this->assertNull($dto->non_existing);
    }

    /** @test */
    public function it_handles_empty_array()
    {
        $dto = new ArrayDTO([]);

        $this->assertEmpty($dto->toArray());
        $this->assertNull($dto->any_key);
    }

    /** @test */
    public function it_preserves_nested_arrays()
    {
        $nestedData = [
            'user' => [
                'name' => 'John',
                'roles' => ['admin', 'editor']
            ],
            'settings' => [
                'notifications' => true
            ]
        ];

        $dto = new ArrayDTO($nestedData);

        // Проверка nested-структур
        $this->assertEquals('John', $dto->user['name']);
        $this->assertContains('admin', $dto->user['roles']);
        $this->assertTrue($dto->settings['notifications']);
    }

    /** @test */
    public function it_handles_special_characters_in_keys()
    {
        $data = [
            'key.with.dots' => 'dot_value',
            'key-with-dashes' => 'dash_value'
        ];

        $dto = new ArrayDTO($data);

        $this->assertEquals('dot_value', $dto->{'key.with.dots'});
        $this->assertEquals('dash_value', $dto->{'key-with-dashes'});
    }
}
