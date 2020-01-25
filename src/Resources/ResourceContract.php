<?php

namespace Lumenite\Neptune\Resources;

use Lumenite\Neptune\ResourceResponse\ClusterResponse;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
interface ResourceContract
{
    /**
     * @param string $file
     * @param array $placeHolders
     * @return mixed
     */
    public function load(string $file, array $placeHolders = []);

    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @param callable $callback
     * @return ClusterResponse
     */
    public function apply(callable $callback);

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
