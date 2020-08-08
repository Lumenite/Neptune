<?php

namespace Lumenite\Neptune\Drivers;

use Lumenite\Neptune\Resources\Resource;

/**
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
interface DriverContract
{
    public function get(Resource $resource, callable $callback = null);
    public function apply(Resource $resource, callable $callback = null);
    public function delete(Resource $resource, callable $callback = null);
    public function wait(Resource $resource, callable $callback = null);
    public function logs(Resource $resource, string $container, ?callable $callback);
    public function getResponse();
}
