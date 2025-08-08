<?php
declare(strict_types=1);

namespace Apd\Trenergy\Exceptions;

use Exception;

class TrenergyServerErrorException extends Exception
{
    public function __construct($message = "Custom error occurred", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
