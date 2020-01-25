<?php

namespace Lumenite\Neptune\Commands;

use Illuminate\Console\Command as LaravelCommand;
use Lumenite\Neptune\ResourceLoader;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
abstract class Command extends LaravelCommand
{
    /** @var ResourceLoader $resourceLoader */
    protected $resourceLoader;

    /**
     * @return ResourceLoader
     */
    public function getResourceLoader()
    {
        if ($this->resourceLoader) {
            return $this->resourceLoader;
        }

        return $this->resourceLoader = app(ResourceLoader::class)
            ->setReleaseName($this->argument('app'))
            ->setVersion($this->argument('version'));
    }
}
