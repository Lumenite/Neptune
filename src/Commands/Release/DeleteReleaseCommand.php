<?php

namespace Lumenite\Neptune\Commands\Release;

use Exception;
use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\Release;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class DeleteReleaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'release:delete {app} {version?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroy release(Deployment, Job, PVC, Secret, Config) from the kubernetes cluster.';

    /**
     * @param Release $release
     * @return bool
     * @throws Exception
     */
    public function handle(Release $release)
    {
        $release->load($this->argument('app'), $this->argument('version'));

        if (!$this->confirm('Deleting a release is irreversible command. Are you sure?')) {
            return false;
        }

        try {
            $release->getApp()->delete(function ($stdout) {
                $this->info($stdout);
            });
        } catch (Exception $exception) {
            $this->warn($exception->getMessage());
        }

        try {
            $release->getService()->delete(function ($stdout) {
                $this->info($stdout);
            });
        } catch (Exception $exception) {
            $this->warn($exception->getMessage());
        }

        try {
            $release->getArtifact()->delete(function ($stdout) {
                $this->info($stdout);
            });
        } catch (Exception $exception) {
            $this->warn($exception->getMessage());
        }

        try {
            $release->getConfig()->delete(function ($stdout) {
                $this->info($stdout);
            });
        } catch (Exception $exception) {
            $this->warn($exception->getMessage());
        }

        try {
            $release->getSecret()->delete(function ($stdout) {
                $this->info($stdout);
            });
        } catch (Exception $exception) {
            $this->warn($exception->getMessage());
        }

        try {
            $release->getDisk()->delete(function ($stdout) {
                $this->info($stdout);
            });
        } catch (Exception $exception) {
            $this->warn($exception->getMessage());
        }
    }
}
