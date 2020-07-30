<?php

namespace Lumenite\Neptune;

use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * @author Mohammed Mudassir <hello@mudasir.me>
 */
class Process extends SymfonyProcess
{
    /**
     * @param string|array $command
     */
    public function __construct($command)
    {
        if (is_string($command)) {
            $command = explode(' ', $command);
        }

        parent::__construct($command);

        set_time_limit(0);
        $this->setTimeout(null);
        $this->setIdleTimeout(null);
        $this->enableOutput();
    }
}
