<?php

namespace Lumenite\Neptune\Resources;

class Deployment extends Resource
{
    /**
     * @return string
     */
    public function getKind()
    {
        return 'deployment';
    }
}
