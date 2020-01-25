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

    protected $version = null;

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
     * @return string
     * @throws Exception
     */
    public function getServicePath()
    {
        return $this->getReleasePath(Release::SECRET_FILE);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAppPath()
    {
        return $this->getReleasePath(Release::APP_FILE);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSecretPath()
    {
        return $this->getReleasePath(Release::SECRET_FILE);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getConfigPath()
    {
        return $this->getReleasePath(Release::CONFIG_FILE);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getArtifactPath()
    {
        return $this->getReleasePath(Release::ARTIFACT_FILE);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDiskPath()
    {
        return $this->getReleasePath(Release::DISK_FILE);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getValues()
    {
        if (!$this->values) {
            $values = $this->yaml::parseFile($this->getReleasePath('values.yml'));
            $primaryValues = [
                'version' => 'v' . ($this->version ?? $values['version']),
            ];

            return $this->values = array_merge($values, $primaryValues);
        }

        return $this->values;
    }

    /**
     * @param string $suffix
     * @return string
     * @throws Exception
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
