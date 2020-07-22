<?php

namespace Lumenite\Neptune\Commands\Resources;

use Lumenite\Neptune\Commands\Command;
use Lumenite\Neptune\Resources\ResourceContract;
use Lumenite\Neptune\Resources\Secret;

/**
 * @package Lumenite\Neptune
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class SecretCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:secret %s';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy/Delete Secret resource from the kubernetes cluster.';

    /**
     * @param Secret $secret
     * @throws \Exception
     */
    public function handle(Secret $secret)
    {
        $secret = $secret->load(
            $this->getResourceLoader()->getSecretPath(),
            $this->getResourceLoader()->getValues()
        );

        $this->{$this->option('delete') ? 'delete' : 'apply'}($secret);
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract|Secret $secret
     * @return mixed|void
     */
    protected function apply(ResourceContract $secret)
    {
        $response = $secret->apply();

        $this->info("Secret resource {$response->name()} deployed successfully.");
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract|Secret $secret
     * @return mixed|void
     */
    protected function delete(ResourceContract $secret)
    {
        $secret->delete(function ($stdout) {
            $this->info($stdout);
        });
    }
}
