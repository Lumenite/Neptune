<?php

namespace Lumenite\Neptune\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Lumenite\Neptune\Exceptions\NotFoundException;
use Lumenite\Neptune\Exceptions\ResourceDeploymentException;
use Lumenite\Neptune\Release;
use Lumenite\Neptune\ResourceLoader;
use Symfony\Component\Process\Process;

/**
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class SyncConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:sync {app}
    {--production : Overwrite the existing namespace and deploy on production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View the total release which are published or waiting to be deployed.';

    /**
     * @param \Lumenite\Neptune\ResourceLoader $resourceLoader
     * @throws \Lumenite\Neptune\Exceptions\NotFoundException
     */
    public function handle(ResourceLoader $resourceLoader)
    {
        tap($resourceLoader->setReleaseName($this->argument('app')), function ($resourceLoader) {
            if ($this->option('production')) {
                $resourceLoader->setNamespace('production');
            }
        });

        $values = $resourceLoader->getValues();

        $awsS3Copy = [
            'aws',
            "--profile={$values['aws_profile']}",
            's3',
            'cp',
        ];

        foreach ([Release::VALUES_FILE, Release::SECRET_FILE] as $file) {
            try {
                $process = new Process(
                    array_merge($awsS3Copy, [
                            $resourceLoader->getReleasePath($file),
                            "s3://{$values['aws_s3_bucket']}/{$values['namespace']}/{$values['name']}/$file",
                        ]
                    )
                );
            } catch (NotFoundException $exception) {
                continue;
            }

            $process->enableOutput();

            $process->run(function ($status, $stdout) {
                if ($status !== Process::OUT) {
                    throw new ResourceDeploymentException($stdout);
                }

                $this->line(trim($stdout));
            });
        }
    }
}
