<?php

namespace Lumenite\Neptune;

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class Values implements Arrayable
{
    /** @var \Illuminate\Support\Collection $properties */
    protected $properties;

    public function __construct($file, $primaryValues = [])
    {
        $this->properties = collect(Yaml::parseFile($file));

        $this->properties['namespace'] = $primaryValues['namespace'] ?? $this->properties['namespace'];
        $this->properties['version'] = 'v' . ($primaryValues['version'] ?? $this->properties['version']);
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


    public function toArray()
    {
        return $this->properties->toArray();
    }
}
