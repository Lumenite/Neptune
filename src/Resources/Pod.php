<?php

namespace Lumenite\Neptune\Resources;

/**
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class Pod extends Resource
{
    /**
     * @return string
     */
    public function getKind()
    {
        return 'pod';
    }
}
