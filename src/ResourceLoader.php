<?php

namespace Lumenite\Neptune;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Lumenite\Neptune\Exceptions\NotFoundException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Yaml\Yaml;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class ResourceLoader implements Arrayable
{
    /** @var string */
    protected $releaseName;

    /** @var null|string $appNamespace */
    protected $version = null;

    /** @var null|string $appNamespace */
    protected $appNamespace = null;

    /** @var \Lumenite\Neptune\Values $values */
    protected $values;

    /** @var Yaml $yaml */
    protected $yaml;

    /** @var string $content */
    protected $content;

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
    public function getValuesFilePath()
    {
        return $this->getReleasePath(Release::VALUES_FILE);
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
            return $this->getReleasePath(Release::DEPLOYMENT_FILE);
        } catch (NotFoundException $exception) {
            return $this->getReleasePath(Release::APP_FILE);
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
     * @return array|\Lumenite\Neptune\Values
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getValues()
    {
        if (!$this->values) {
            return $this->values = new Values($this->getReleasePath(Release::VALUES_FILE), [
                'namespace' => $this->appNamespace,
                'version' => $this->version,
            ]);
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
        return $this->getValues()->get($key);
    }

    /**
     * @param string $suffix
     * @return string
     * @throw \Exception
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function getReleasePath($suffix = '')
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

    /**
     * @param string $file
     * @param array|\Lumenite\Neptune\Values $placeholders
     * @return $this
     */
    public function load($file, Values $placeholders)
    {
        $this->content = $this->replacePlaceholders(file_get_contents($file), $placeholders->toArray());

        return $this;
    }

    /**
     * @param string $content
     * @param array $placeholders
     * @param string $suffix`
     * @return string|string[]|null
     */
    protected function replacePlaceholders(string $content, array $placeholders, string $suffix = '')
    {
        foreach ($placeholders as $key => $value) {
            if (is_array($value)) {
                return $this->replacePlaceholders($content, $value, ".$key");
            }

            $content = preg_replace("/\{\{\s?{$suffix}\.{$key}\s?\}\}/i", $value, $content);
        }
        return $content;
    }

    /**
     * @return array|mixed
     */
    public function toArray()
    {
        if (!$this->content) {
            throw new NotFoundResourceException('Resource not loaded.');
        }

        return $this->yaml::parse($this->content);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }
}
