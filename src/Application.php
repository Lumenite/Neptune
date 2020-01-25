<?php

namespace Lumenite\Neptune;

use Laravel\Lumen\Application as LumenApplication;

class Application extends LumenApplication
{
    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return 'Neptune (0.1) (Laravel Components ^6.0)';
    }

    /**
     * Prepare the application to execute a console command.
     *
     * @param  bool  $aliases
     * @return void
     */
    public function prepareForConsoleCommand($aliases = true)
    {
    }
}
