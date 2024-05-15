<?php

namespace Botble\Member\Providers;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Member\Listeners\UpdatedContentListener;
use Botble\Member\Listeners\UpdatedCollectionListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
            UpdatedCollectionListener::class,
        ],
    ];
}
