<?php

namespace Bfg\BlessModel;

use Bfg\BlessModel\ApplyLevy\SaveLevyModelCollectionListener;
use Bfg\BlessModel\ApplyLevy\SaveLevyModelListener;
use Bfg\Installer\Providers\InstalledProvider;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 * @package Bfg\BlessModel
 */
class ServiceProvider extends InstalledProvider
{
    /**
     * The description of extension.
     * @var string|null
     */
    public ?string $description = "To quickly save data in the model";

    /**
     * Set as installed by default.
     * @var bool
     */
    public bool $installed = true;

    /**
     * Executed when the provider is registered
     * and the extension is installed.
     * @return void
     */
    function installed(): void
    {
        \Event::listen(LevyModel::class, SaveLevyModelListener::class);
        \Event::listen(LevyModelCollection::class, SaveLevyModelCollectionListener::class);
    }

    /**
     * Executed when the provider run method
     * "boot" and the extension is installed.
     * @return void
     */
    function run(): void
    {

    }
}
