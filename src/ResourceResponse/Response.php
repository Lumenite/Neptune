<?php

namespace Lumenite\Neptune\ResourceResponse;

use Illuminate\Support\Collection;

/**
 * @package Modelizer\Lanton\ResourceResponse
 */
class Response implements ClusterResponse
{
    /** @var Collection $response */
    protected $response;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = collect($response ?? []);
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->response['metadata']['name'];
    }

    /**
     * @return mixed
     */
    public function kind()
    {
        return $this->response['kind'];
    }

    /**
     * @return Collection
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->response['status']['phase'] === 'Pending';
    }
}
