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

        if (@$this->response['status']['active'] != 1) {
            return true;
        }

        return false;
    }
}
