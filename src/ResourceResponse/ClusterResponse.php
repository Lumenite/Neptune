<?php

namespace Lumenite\Neptune\ResourceResponse;

use Illuminate\Support\Collection;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
interface ClusterResponse
{
    /**
     * @return mixed
     */
    public function name();

    /**
     * @return mixed
     */
    public function kind();

    /**
     * @return Collection
     */
    public function response();

    /**
     * @return bool
     */
    public function isPending();
}
