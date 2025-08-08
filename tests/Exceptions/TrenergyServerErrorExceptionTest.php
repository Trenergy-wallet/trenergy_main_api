<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\Exceptions;

use Apd\Trenergy\Exceptions\TrenergyServerErrorException;
use Exception;
use PHPUnit\Framework\TestCase;

class TrenergyServerErrorExceptionTest extends TestCase
{
    /** @test */
    public function it_creates_exception_with_default_values()
    {
        $exception = new TrenergyServerErrorException();

        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertEquals('Custom error occurred', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}
