<?php

namespace Lumenite\Neptune\Drivers\Events;

use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels;
}
