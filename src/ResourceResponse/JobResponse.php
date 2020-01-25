<?php

namespace Lumenite\Neptune\ResourceResponse;

/**
 * @package Lumenite\Neptune\ResourceResponse
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class JobResponse extends Response
{
    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->response['status']['active'] != 1;
    }
}
