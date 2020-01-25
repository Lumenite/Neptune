<?php

namespace Lumenite\Neptune\Resources;

use Lumenite\Neptune\ResourceResponse\JobResponse;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class Job extends Resource
{
    /**
     * @return string
     */
    public function getKind()
    {
        return 'job';
    }

    /**
     * @inheritdoc
     */
    public function getResponseClass()
    {
        return JobResponse::class;
    }
}
