<?php

namespace Lumenite\Neptune\Commands\Release;

use Illuminate\Filesystem\Filesystem;
use Lumenite\Neptune\Commands\Command;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class MakeReleaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'release:make {app} {version=0.1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a release to deploy on kubernetes cluster';

    /**
     * @param Filesystem $filesystem
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(Filesystem $filesystem)
    {
        $placeHolders = [
            'name' => $this->argument('app'),
            'version' => $this->argument('version'),
        ];

        $releasePath = NEPTUNE_EXEC_PATH . "/kubernetes/{$this->argument('app')}";
        $stubPath =  base_path('stubs');

        if ($filesystem->isDirectory($releasePath)) {
            if ($this->confirm("$releasePath directory already exists. Would you like to overwrite it?")) {
                $this->copyReleaseFiles($filesystem, $releasePath, $stubPath);
            }
        } else {
            $this->copyReleaseFiles($filesystem, $releasePath, $stubPath);
        }

        $values = $filesystem->get("$releasePath/values.yml");

        foreach ($placeHolders as $key => $placeHolder) {
            $values = str_replace("{{ .$key }}", $placeHolder, $values);
        }

        $filesystem->put("$releasePath/values.yml", $values);

        if (!$filesystem->isDirectory(NEPTUNE_EXEC_PATH . '/storage/k8s')) {
            $filesystem->makeDirectory(NEPTUNE_EXEC_PATH . '/storage/k8s', 0755, true, true);
            $filesystem->copyDirectory($stubPath . '/k8s', NEPTUNE_EXEC_PATH . '/storage/k8s');
        }

        $this->info("{$this->argument('app')} release build successfully.");
    }

    /**
     * @param Filesystem $filesystem
     * @param string $releasePath
     * @param string $stubPath
     */
    protected function copyReleaseFiles(Filesystem $filesystem, string $releasePath, string $stubPath): void
    {
        $filesystem->makeDirectory($releasePath, 0755, true, true);
        $filesystem->copyDirectory($stubPath . '/__app__', $releasePath);
    }
}
