<?php

namespace Lumenite\Neptune\Resources;

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
