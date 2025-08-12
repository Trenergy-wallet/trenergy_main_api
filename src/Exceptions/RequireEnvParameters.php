<?php
declare(strict_types=1);

namespace Apd\Trenergy\Exceptions;

use Apd\Trenergy\Enums\Consumers\PaymentPeriod;
use Exception;

class RequireEnvParameters extends Exception
{
    public function __construct(
        $message = null,
        $code = 0,
        Exception $previous = null
    ) {
        if (!$message) {
            $message = 'Required env parameters is missing. Please obtain and set TRENERGY_API_URL and TRENERGY_API_KEY';
        }
        parent::__construct($message, $code, $previous);
    }
}
