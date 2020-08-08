<?php

namespace Lumenite\Neptune\Drivers;

use Lumenite\Neptune\Resources\Resource;

/**
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class Api implements DriverContract
{
    public function get(Resource $resource, callable $callback = null)
    {
        // TODO: Implement get() method.
    }

    public function apply(Resource $resource, callable $callback = null)
    {
        // TODO: Implement apply() method.
    }

    public function delete(Resource $resource, callable $callback = null)
    {
        // TODO: Implement delete() method.
    }

    public function wait(Resource $resource, callable $callback = null)
    {
        // TODO: Implement wait() method.
    }

    public function logs(Resource $resource, string $container, ?callable $callback)
    {
        // TODO: Implement logs() method.
    }

    public function getResponse()
    {
        // TODO: Implement getResponse() method.
    }
}
