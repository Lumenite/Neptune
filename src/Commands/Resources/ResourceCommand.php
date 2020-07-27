<?php

namespace Lumenite\Neptune\Commands\Resources;

use Illuminate\Console\Command;
use Lumenite\Neptune\Exceptions\DeploymentTerminatedException;
use Lumenite\Neptune\ResourceLoader;
use Lumenite\Neptune\Resources\ResourceContract;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
abstract class ResourceCommand extends Command
{
    /** @var string $defaultSignatureSuffix */
    protected $defaultSignatureSuffix = '
    {app}
    {version?}
    {--delete : Delete the given resource}
    {--production : Overwrite the existing namespace and deploy on production}';

    /** @var ResourceLoader $resourceLoader */
    protected $resourceLoader;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->signature = sprintf($this->signature, $this->defaultSignatureSuffix);

        parent::__construct();
    }

    /**
     * @return \Lumenite\Neptune\ResourceLoader
     * @throws \Lumenite\Neptune\Exceptions\DeploymentTerminatedException
     */
    public function getResourceLoader()
    {
        if (!$this->resourceLoader) {
            $this->loadResourceLoader();
        }

        return $this->resourceLoader;
    }

    /**
     * @return $this|\Lumenite\Neptune\ResourceLoader
     * @throws \Lumenite\Neptune\Exceptions\DeploymentTerminatedException
     */
    public function loadResourceLoader()
    {
        if ($this->option('production')) {
            if (!$this->confirm('Deployment will be deploy on production. Are you sure?')) {
                throw new DeploymentTerminatedException;
            }
        }

        if ($this->resourceLoader) {
            return $this->resourceLoader;
        }

        /** @var ResourceLoader $resourceLoader */
        $this->resourceLoader = ($resourceLoader = app(ResourceLoader::class))
            ->setReleaseName($this->argument('app'))
            ->setVersion($this->argument('version'))
            ->setNamespace($this->option('production') ? 'production' : null);

        return $this;
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract $resource
     * @throws \Lumenite\Neptune\Exceptions\DeploymentTerminatedException
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function defaultHandle(ResourceContract $resource)
    {
        $this->loadResourceLoader();

        $deployment = $resource->load(
            $this->resourceLoader->{'get' . $resource->getKind() . 'Path'}(),
            $this->resourceLoader->getValues()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($deployment);
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract $resource
     * @return mixed
     */
    abstract protected function apply(ResourceContract $resource);

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract $resource
     * @return mixed
     */
    abstract protected function delete(ResourceContract $resource);
}
