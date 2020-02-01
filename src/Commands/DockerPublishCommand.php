<?php

namespace Lumenite\Neptune\Commands;

use Illuminate\Filesystem\Filesystem;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class DockerPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docker:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish docker files.';

    /**
     * @param Filesystem $filesystem
     */
    public function handle(Filesystem $filesystem)
    {
        $dockerPath = NEPTUNE_EXEC_PATH . "/docker";
        $stubPath = base_path('/stubs');

        if ($filesystem->isDirectory($dockerPath)) {
            if ($this->confirm("$dockerPath directory already exists. Would you like to overwrite it?")) {
                $this->copyDockerFiles($filesystem, $dockerPath, $stubPath);
            }
        } else {
            $this->copyDockerFiles($filesystem, $dockerPath, $stubPath);
        }

        if ($filesystem->isFile(NEPTUNE_EXEC_PATH . '/docker-compose.yml')) {
            if ($this->confirm(NEPTUNE_EXEC_PATH . "/docker-compose.yml directory already exists. Would you like to overwrite it?")) {
                $this->copyDockerComposeFile($filesystem, $stubPath);
            }
        } else {
            $this->copyDockerComposeFile($filesystem, $stubPath);
        }

        $this->info("Docker file publish successfully.");
    }

    /**
     * @param Filesystem $filesystem
     * @param string $dockerPath
     * @param string $stubPath
     */
    protected function copyDockerFiles(Filesystem $filesystem, string $dockerPath, string $stubPath): void
    {
        $filesystem->copyDirectory($stubPath . '/docker', $dockerPath);
    }

    /**
     * @param Filesystem $filesystem
     * @param string $stubPath
     */
    protected function copyDockerComposeFile(Filesystem $filesystem, string $stubPath): void
    {
        $filesystem->copy($stubPath . '/docker-compose.yml', NEPTUNE_EXEC_PATH . '/docker-compose.yml');
    }
}
