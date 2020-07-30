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
     * @return array
     */
    public function toArray()
    {
        return $this->properties->toArray();
    }
}
