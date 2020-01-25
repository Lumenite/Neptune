<?php

namespace Lumenite\Neptune\Resources;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class Service extends Resource
{
    /**
     * @return string
     */
    public function getKind()
    {
        return 'service';
    }
}
