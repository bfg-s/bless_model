<?php

namespace Bfg\BlessModel;

use Bfg\BlessModel\Core\BlessBuilderCore;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class BlessModel
 * @package Bfg\BlessModel
 */
class BlessModel extends BlessBuilderCore
{
    /**
     * Bless data for one model
     *
     * @param  Model|Relation|Builder|string  $model
     * @param  Arrayable|string|array  $data
     * @return mixed
     */
    public function do(Model|Relation|Builder|string $model, Arrayable|string|array $data): mixed
    {
        return $this->applyLevy(
            $this->makeLevyCollection($model, $data)
        );
    }
}
