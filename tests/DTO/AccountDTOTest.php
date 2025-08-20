<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\DTO;

use Apd\Trenergy\Tests\TestCase;
use Apd\Trenergy\DTO\Account\AccountDTO;

class AccountDTOTest extends TestCase
{
    /** @test */
    public function it_creates_dto_from_array()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'balance' => 100.50,
            'has_password' => true,
            'is_banned' => false
        ];

        $dto = new AccountDTO($data);

        $this->assertEquals('Test User', $dto->name);
        $this->assertEquals('test@example.com', $dto->email);
        $this->assertEquals(100.50, $dto->balance);
        $this->assertTrue($dto->has_password);
        $this->assertFalse($dto->is_banned);
    }

    /** @test */
    public function it_handles_missing_fields()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];

        $dto = new AccountDTO($data);

        $this->assertEquals('Test User', $dto->name);
        $this->assertEquals(0.0, $dto->balance); // Default value
    }
}
