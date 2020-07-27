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

    /**
     * @param \Lumenite\Neptune\ResourceLoader $resourceLoader
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param \Lumenite\Neptune\Kubectl $kubectl
     */
    public function __construct(ResourceLoader $resourceLoader, Filesystem $filesystem, Kubectl $kubectl)
    {
        $this->resourceLoader = $resourceLoader;
        $this->filesystem = $filesystem;
        $this->kubectl = $kubectl;
    }

    /**
     * @param string $file
     * @param array $placeHolders
     * @return $this|mixed
     */
    public function load(string $file, array $placeHolders = [])
    {
        $this->config = collect($content = $this->resourceLoader->load($file, $placeHolders));

        $this->filesystem->put(
            $this->filePath = NEPTUNE_EXEC_PATH. "/storage/k8s/{$this->getName()}.{$this->getKind()}.yml",
            $content
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function apply(callable $callback = null)
    {
        return $this->kubectl->apply($this, $callback);
    }

    /**
     * @inheritdoc
     */
    public function delete(callable $callback = null)
    {
        return $this->kubectl->delete($this, $callback);
    }

    /**
     * @inheritdoc
     */
    public function get(callable $callback = null)
    {
        return $this->kubectl->get($this, $callback);
    }

    /**
     * @param callable|null $callback
     * @return Kubectl|ResourceContract
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function wait(callable $callback = null)
    {
        return $this->kubectl->wait($this, $callback);
    }

    /**
     * @param callable|null $callback
     * @return string
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function follow(callable $callback = null)
    {
        return $this->kubectl->logs($this, $callback)->getOutput();
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
