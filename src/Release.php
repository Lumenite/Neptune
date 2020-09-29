<?php

namespace Lumenite\Neptune;

use Lumenite\Neptune\Exceptions\NotFoundException;
use Lumenite\Neptune\Resources\ConfigMap;
use Lumenite\Neptune\Resources\Deployment;
use Lumenite\Neptune\Resources\Job;
use Lumenite\Neptune\Resources\PersistentVolumeClaim;
use Lumenite\Neptune\Resources\Secret;
use Lumenite\Neptune\Resources\Service;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class Release
{
    /**
     * ConfigMap file for the application.
     *
     * @var string
     */
    const CONFIG_FILE = 'config.yml';

    /**
     * Secret file for the application.
     *
     * @var string
     */
    const SECRET_FILE = 'secret.yml';

    /**
     * Persistent volume claim file for the application which will be mounted on artifact to build it.
     *
     * @var string
     */
    const DISK_FILE = 'disk.yml';

    /**
     * Job file to build artifact for the application
     *
     * @var string
     */
    const ARTIFACT_FILE = 'artifact.yml';

    /**
     * Service file for the application.
     *
     * @var string
     */
    const SERVICE_FILE = 'service.yml';

    /**
     * Horizontal pod autoscaling file for the application.
     *
     * @var string
     */
    const HPA_FILE = 'hpa.yml';

    /**
     * Deployment file which will handle the application request
     *
     * @deprecated in favour of DEPLOYMENT_FILE
     * @var string
     */
    const APP_FILE = 'app.yml';

    /**
     * Deployment file which will handle the application request
     *
     * @var string
     */
    const DEPLOYMENT_FILE = 'deployment.yml';

    /**
     * Values file is the holder of application configuration.
     * Please note directly is it not the part of kubernetes resource
     *
     * @var string
     */
    const VALUES_FILE = 'values.yml';

    /** @var string $name Name of the Release */
    protected $name;

    /** @var string $version */
    protected $version;

    /** @var ResourceLoader $resourceLoader */
    protected $resourceLoader;

    /** @var ConfigMap $config */
    protected $config;

    /** @var Secret $secret */
    protected $secret;

    /** @var PersistentVolumeClaim $disk */
    protected $disk;

    /** @var Job $artifact */
    protected $artifact;

    /** @var Service $service */
    protected $service;

    /** @var Deployment $app */
    protected $app;

    /** @var bool $onProduction */
    protected $onProduction;

    /**
     * Set callback when file not found exception is thrown while load the file.
     * Note: In release only secret and config are
     *
     * @var callable $fileNotFindFailure
     */
    protected $fileNotFindFailure;

    /**
     * @param ResourceLoader $resourceLoader
     * @param ConfigMap $config
     * @param Secret $secret
     * @param PersistentVolumeClaim $persistentVolumeClaim
     * @param Job $job
     * @param Deployment $deployment
     * @param Service $service
     */
    public function __construct(
        ResourceLoader $resourceLoader,
        ConfigMap $config,
        Secret $secret,
        PersistentVolumeClaim $persistentVolumeClaim,
        Job $job,
        Service $service,
        Deployment $deployment)
    {
        $this->resourceLoader = $resourceLoader;
        $this->config = $config;
        $this->secret = $secret;
        $this->disk = $persistentVolumeClaim;
        $this->artifact = $job;
        $this->service = $service;
        $this->app = $deployment;
    }

    /**
     * @return ConfigMap
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Secret
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return PersistentVolumeClaim
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * @return Job
     */
    public function getArtifact()
    {
        return $this->artifact;
    }

    /**
     * @return Deployment
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->resourceLoader->setReleaseName($name);
        $this->name = $name;

        return $this;
    }

    /**
     * @param $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->resourceLoader->setVersion($version);
        $this->version = $version;

        return $this;
    }

    /**
     * @return $this
     */
    public function onProduction()
    {
        $this->resourceLoader->setNamespace('production');
        $this->onProduction = true;

        return $this;
    }

    /**
     * @param $name
     * @param null $version
     * @return $this
     * @throws \Exception
     */
    public function load($name, $version = null)
    {
        $this->setName($name)
            ->setVersion($version)
            ->catchFileNotFoundException(function () {
                $this->config->load($this->resourceLoader->getConfigPath(), $this->resourceLoader);
            })
            ->catchFileNotFoundException(function () {
                $this->secret->load($this->resourceLoader->getSecretPath(), $this->resourceLoader);
            });

        $this->disk->load($this->resourceLoader->getDiskPath(), $this->resourceLoader);
        $this->artifact->load($this->resourceLoader->getArtifactPath(), $this->resourceLoader);
        $this->service->load($this->resourceLoader->getServicePath(), $this->resourceLoader);
        $this->app->load($this->resourceLoader->getDeploymentPath(), $this->resourceLoader);

        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setFileNotFoundFailure(callable $callback)
    {
        $this->fileNotFindFailure = $callback;

        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    protected function catchFileNotFoundException(callable $callback)
    {
        try {
            $callback();
        } catch (NotFoundException $exception) {
            if ($this->fileNotFindFailure) {
                call_user_func($this->fileNotFindFailure, $exception);
            }
        }

        return $this;
    }
}
