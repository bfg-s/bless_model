<?php

namespace Bfg\BlessModel;

use Bfg\BlessModel\ApplyLevy\SaveLevyModelCollectionListener;
use Bfg\BlessModel\ApplyLevy\SaveLevyModelListener;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 * @package Bfg\BlessModel
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register route settings.
     * @return void
     */
    public function register()
    {
        \Event::listen(LevyModel::class, SaveLevyModelListener::class);
        \Event::listen(LevyModelCollection::class, SaveLevyModelCollectionListener::class);
    }

    /**
     * Bootstrap services.
     * @return void
     */
    public function boot()
    {

    }
}
