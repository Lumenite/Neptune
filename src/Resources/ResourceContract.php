<?php

namespace Lumenite\Neptune\Resources;

use Lumenite\Neptune\ResourceLoader;
use Lumenite\Neptune\ResourceResponse\ClusterResponse;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
interface ResourceContract
{
    /**
     * @param string $file
     * @param \Lumenite\Neptune\ResourceLoader $resourceLoader
     * @return mixed
     */
    public function load(string $file, ResourceLoader $resourceLoader);

    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @param callable|null $callback
     * @return mixed
     */
    public function apply(callable $callback = null);

    /**
     * @param callable|null $callback
     * @return mixed
     */
    public function delete(callable $callback = null);

    /**
     * @param callable $callback
     * @return ClusterResponse
     */
    public function get(callable $callback);

    /**
     * @param callable $callback
     * @return self
     */
    public function wait(callable $callback);

    /**
     * @return string
     */
    public function getKind();

    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getVersion();

    /**
     * @return string
     */
    public function getResponseClass();
}
