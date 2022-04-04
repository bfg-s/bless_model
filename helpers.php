<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

if (! function_exists('bless_model')) {
    /**
     * @template MODEL_TEMPLATE
     * @param  MODEL_TEMPLATE|Model|Relation|Builder|string  $model
     * @param  Arrayable|string|array  $data
     * @return MODEL_TEMPLATE|mixed
     */
    function bless_model(
        Model|Relation|Builder|string $model,
        Arrayable|string|array $data
    ): mixed {
        return \Bfg\BlessModel\Facades\BlessModel::do($model, $data);
    }
}
