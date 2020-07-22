<?php

namespace Lumenite\Neptune;

use Exception;
use Lumenite\Neptune\Exceptions\NotFoundException;
use Symfony\Component\Yaml\Yaml;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class ResourceLoader
{
    /** @var string */
    protected $releaseName;

    /** @var null|string $appNamespace */
    protected $version = null;

    /** @var null|string $appNamespace */
    protected $appNamespace = null;

    /** @var array */
    protected $values;

    /** @var Yaml $yaml */
    protected $yaml;

    /**
     * @param Yaml $yaml
     */
    public function __construct(Yaml $yaml)
    {
        $this->yaml = $yaml;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setReleaseName($name)
    {
        $this->releaseName = $name;

        return $this;
    }

    /**
     * @param $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @param string|null $namespace
     * @return $this
     */
    public function setNamespace(?string $namespace)
    {
        $this->appNamespace = $namespace;

        return $this;
    }

    /**
     * @return string
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getServicePath()
    {
        return $this->getReleasePath(Release::SERVICE_FILE);
    }

    /**
     * @return string
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getHpaPath()
    {
        return $this->getReleasePath(Release::HPA_FILE);
    }

    /**
     * @return string
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getDeploymentPath()
    {
        try {
            return $this->getReleasePath(Release::APP_FILE);
        } catch (NotFoundException $exception) {
            return $this->getReleasePath(Release::DEPLOYMENT_FILE);
        }
    }

    /**
     * @return string
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getSecretPath()
    {
        return $this->getReleasePath(Release::SECRET_FILE);
    }

    /**
     * @return string
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getConfigPath()
    {
        return $this->getReleasePath(Release::CONFIG_FILE);
    }

    /**
     * @return string
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getArtifactPath()
    {
        return $this->getReleasePath(Release::ARTIFACT_FILE);
    }

    /**
     * @return string
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getDiskPath()
    {
        return $this->getReleasePath(Release::DISK_FILE);
    }

    /**
     * @return array
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getValues()
    {
        if (!$this->values) {
            $values = $this->yaml::parseFile($this->getReleasePath('values.yml'));
            $primaryValues = [
                'namespace' =>  $this->appNamespace ?? $values['namespace'],
                'version' => 'v' . ($this->version ?? $values['version']),
            ];

            return $this->values = array_merge($values, $primaryValues);
        }

        return $this->values;
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function get($key)
    {
        return $this->getValues()[$key];
    }

    /**
     * @param string $suffix
     * @return string
     * @throw \Exception
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    protected function getReleasePath($suffix = '')
    {
        if (!$this->releaseName) {
            throw new Exception('Release name is not set.');
        }

        if (is_file($path = "kubernetes/{$this->releaseName}/$suffix")) {
            return $path;
        }

        if (is_dir($path)) {
            return $path;
        }

        throw new NotFoundException("$path is not present");
    }
}
