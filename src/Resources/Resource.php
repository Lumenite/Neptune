<?php

namespace Lumenite\Neptune\Resources;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Lumenite\Neptune\Kubectl;
use Lumenite\Neptune\ResourceLoader;
use Lumenite\Neptune\ResourceResponse\Response;
use Symfony\Component\Yaml\Yaml;

/**
 * A kubernetes resource abstraction layer.
 *
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
abstract class Resource implements ResourceContract
{
    /** @var ResourceLoader $resourceLoader */
    protected $resourceLoader;

    /** @var Filesystem $filesystem */
    protected $filesystem;

    /** @var Kubectl $kubectl */
    protected $kubectl;

    /** @var string */
    protected $filePath;

    /** @var Collection $config */
    protected $config;

    /** @var \Lumenite\Neptune\Values $values */
    protected $values;

    /**
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param \Lumenite\Neptune\Kubectl $kubectl
     */
    public function __construct(Filesystem $filesystem, Kubectl $kubectl)
    {
        $this->filesystem = $filesystem;
        $this->kubectl = $kubectl;
    }

    /**
     * @param string $file
     * @param \Lumenite\Neptune\ResourceLoader $resourceLoader
     * @return $this|mixed
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function load(string $file, ResourceLoader $resourceLoader)
    {
        $this->config = collect($resourceLoader->load($file, $this->values = $resourceLoader->getValues()));

        $this->filesystem->put(
            $this->filePath = NEPTUNE_EXEC_PATH. "/storage/k8s/{$this->getName()}.{$this->getKind()}.yml",
            $resourceLoader
        );

        return $this;
    }

    /**
     * @param callable|null $callback
     * @return \Lumenite\Neptune\ResourceResponse\ClusterResponse|mixed
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function apply(callable $callback = null)
    {
        return $this->kubectl->apply($this, $callback);
    }

    /**
     * @param callable|null $callback
     * @return \Lumenite\Neptune\ResourceResponse\ClusterResponse|mixed
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function delete(callable $callback = null)
    {
        return $this->kubectl->delete($this, $callback);
    }

    /**
     * @param callable|null $callback
     * @return \Lumenite\Neptune\ResourceResponse\ClusterResponse
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function get(callable $callback = null)
    {
        return $this->kubectl->get($this, $callback);
    }

    /**
     * @param callable|null $callback
     * @return $this|\Lumenite\Neptune\Resources\ResourceContract
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function wait(callable $callback = null)
    {
        $this->kubectl->wait($this, $callback);

        return $this;
    }

    /**
     * @param callable|null $callback
     * @return bool
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function follow(callable $callback = null)
    {
        if (! $job = @$this->values->get('resource_logs')['jobs']) {
            return false;
        }

        foreach ($job as $container) {
            $this->kubectl->logs($this, $container, $callback);
        }

        return true;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->config['metadata']['namespace'];
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->config['metadata']['name'];
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->config['metadata']['version'];
    }

    /**
     * @return string
     */
    public function getResponseClass()
    {
        return Response::class;
    }
}
