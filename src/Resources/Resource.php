<?php

namespace Lumenite\Neptune\Resources;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Lumenite\Neptune\Kubectl;
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
    /** @var Yaml $yaml */
    protected $yaml;

    /** @var Filesystem $filesystem */
    protected $filesystem;

    /** @var Kubectl $kubectl */
    protected $kubectl;

    /** @var string */
    protected $filePath;

    /** @var Collection $config */
    protected $config;

    /**
     * @param Yaml $yaml
     * @param Filesystem $filesystem
     * @param Kubectl $kubectl
     */
    public function __construct(Yaml $yaml, Filesystem $filesystem, Kubectl $kubectl)
    {
        $this->yaml = $yaml;
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
        $content = file_get_contents($file);

        foreach ($placeHolders as $key => $value) {
            $content = str_replace("{{ .$key }}", $value, $content);
        }

        $this->config = collect($this->yaml::parse($content));

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
