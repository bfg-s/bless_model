<?php

namespace Bfg\BlessModel\LevyModelPipes;

use Bfg\BlessModel\LevyModel;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class GeneralModelInformationPipe
 * @package Bfg\BlessModel\LevyModelPipes
 */
class GeneralModelInformationPipe
{
    /**
     * @param  LevyModel  $model
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(LevyModel $model, Closure $next) {

        if ($model->src instanceof Relation) {

            $model->model_class = $model->src->getModel();

        } else if ($model->src instanceof Model) {

            $model->model_class = $model->src;

        } else if ($model->src instanceof Builder) {

            $model->model_class = $model->src->getModel();
        }

        if ($model->model_class) {

            $model->model_class_name = get_class($model->model_class);

            $model->model_table = $model->model_class->getTable();

            $model->model_key = $model->model_class->getKeyName();

            $model->model_exists = $model->model_class->exists;

            $model->model_soft_delete = method_exists($model->model_class, 'trashed');
        }

        return $next($model);
    }
}
