<?php

namespace Lumenite\Neptune\Commands\Release;

use Lumenite\Neptune\Commands\Command;

/**
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class ViewReleaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'release:view {app}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View the total release which are published or waiting to be deployed.';
}
