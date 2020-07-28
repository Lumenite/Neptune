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
        if (@$this->response->get('status')['succeeded']) {
            return false;
        }

        return $this->response['status']['active'] != 1;
    }
}
