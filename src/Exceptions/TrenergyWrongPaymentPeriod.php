<?php
declare(strict_types=1);

namespace Apd\Trenergy\Exceptions;

use Apd\Trenergy\Enums\Consumers\PaymentPeriod;
use Exception;

class TrenergyWrongPaymentPeriod extends Exception
{
    public function __construct(
        $message = null,
        $code = 0,
        Exception $previous = null
    ) {
        if (!$message) {
            $message = 'Wrong Payment Period available payemnt periods is: ' . PaymentPeriod::getValuesAsString();
        }
        parent::__construct($message, $code, $previous);
    }
}
