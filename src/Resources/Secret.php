<?php

namespace Lumenite\Neptune\Resources;

class Secret extends Resource
{
    /**
     * @return string
     */
    public function getKind()
    {
        return 'secret';
    }
}
