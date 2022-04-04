<?php

namespace Bfg\BlessModel\Facades;

use Bfg\BlessModel\BlessModel as BlessModelAccess;
use Illuminate\Support\Facades\Facade as FacadeIlluminate;

class BlessModel extends FacadeIlluminate
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BlessModelAccess::class;
    }
}
