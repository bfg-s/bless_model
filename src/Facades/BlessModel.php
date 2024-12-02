<?php

namespace Bfg\BlessModel\Facades;

use Bfg\BlessModel\BlessModel as BlessModelAccess;
use Illuminate\Support\Facades\Facade as FacadeIlluminate;

/**
 * @extends BlessModelAccess
 */
class BlessModel extends FacadeIlluminate
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return BlessModelAccess::class;
    }
}
