<?php

namespace Lumenite\Neptune;

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Yaml\Yaml;

/**
 * @property $context
 * @property $namespace
 * @property $name
 * @property $version
 * @property $storageClass
 * @property $git_host
 * @property $git_namespace
 * @property $npm_token
 * @property $app_key
 * @property $db_password
 * @property $aws_profile
 * @property $aws_s3_bucket
 * @property $resources
 *
 * @see /stubs/values.yml
 * @see /kubernetes/{your-app-name}/values.yml
 *
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class Values implements Arrayable
{
    /** @var \Illuminate\Support\Collection $properties */
    protected $properties;

    /**
     * @param $file
     * @param array $primaryValues
     */
    public function __construct($file, $primaryValues = [])
    {
        $this->properties = collect(Yaml::parseFile($file));

        foreach ($primaryValues as $key => $value) {
            if ($value) {
                $this->properties[$key] = $value;
            }
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return array
     */
    public function __call($name, $arguments)
    {
        return $this->properties->$name(...$arguments);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->properties[$name];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->properties->toArray();
    }
}
