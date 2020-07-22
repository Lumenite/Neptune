<?php

namespace Lumenite\Neptune\Exceptions;

use Exception;

/**
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class DeploymentTerminatedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Resource Deployment was terminated.');
    }
}
