<?php

namespace Bfg\BlessModel;

use Illuminate\Support\Facades\Facade as FacadeIlluminate;

/**
 * Class Facade
 * @package Bfg\BlessModel
 */
class Facade extends FacadeIlluminate
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BlessModel::class;
    }
}
