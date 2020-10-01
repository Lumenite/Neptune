<?php

namespace Lumenite\Neptune\Exceptions;

use Exception;

class InvalidValueGivenException extends Exception
{
    /**
     * @param $value
     */
    public function __construct($value)
    {
        parent::__construct("You need to set $value in your values.yml file.", 403);
    }
}
