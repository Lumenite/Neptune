<?php

namespace Lumenite\Neptune\Resources;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class AutoScale extends Resource
{
    /**
     * @return string
     */
    public function getKind()
    {
        return 'hpa';
    }
}
