<?php

namespace Lumenite\Neptune\Resources;

use Lumenite\Neptune\ResourceResponse\JobResponse;

/**
 * @package Lumenite\Neptune\Resources
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class PersistentVolume extends Resource
{
    /**
     * @return string
     */
    public function getKind()
    {
        return 'pv';
    }

    /**
     * @return string
     */
    public function getResponseClass()
    {
        return JobResponse::class;
    }
}
