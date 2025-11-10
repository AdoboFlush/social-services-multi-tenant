<?php

namespace App\Exceptions;

use Exception;

class PaymentGatewayException extends Exception
{
    private $_options;

    public function __construct(
        $message,
        $options = [],
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->_options = $options;
    }

    public function getTranslatedMessage(): string
    {
        return _lang($this->getMessage(), $this->_options);
    }
}
