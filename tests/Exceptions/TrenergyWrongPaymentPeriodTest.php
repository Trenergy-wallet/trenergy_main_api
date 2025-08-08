<?php
declare(strict_types=1);

namespace Apd\Trenergy\Tests\Exceptions;

use Apd\Trenergy\Tests\TestCase;
use Apd\Trenergy\Exceptions\TrenergyWrongPaymentPeriod;
use Apd\Trenergy\Enums\Consumers\PaymentPeriod;

class TrenergyWrongPaymentPeriodTest extends TestCase
{
    /** @test */
    public function it_has_default_message()
    {
        $exception = new TrenergyWrongPaymentPeriod();

        $expectedMessage = 'Wrong Payment Period available payemnt periods is: ' .
            implode(', ', array_column(PaymentPeriod::cases(), 'value'));

        $this->assertEquals($expectedMessage, $exception->getMessage());
    }

    /** @test */
    public function it_allows_custom_message()
    {
        $customMessage = 'Custom error message';
        $exception = new TrenergyWrongPaymentPeriod($customMessage);

        $this->assertEquals($customMessage, $exception->getMessage());
    }
}
