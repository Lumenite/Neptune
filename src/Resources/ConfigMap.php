<?php

namespace Lumenite\Neptune\Resources;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class ConfigMap extends Resource
{
    /**
     * @return string
     */
    public function getKind()
    {
        return 'configmaps';
    }
}
