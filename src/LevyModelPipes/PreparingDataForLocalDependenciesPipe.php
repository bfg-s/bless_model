<?php

namespace Bfg\BlessModel\LevyModelPipes;

use Bfg\BlessModel\BlessModel;
use Bfg\BlessModel\LevyModel;
use Closure;

/**
 * Class PreparingDataForLocalDependenciesPipe
 * @package Bfg\BlessModel\LevyModelPipes
 */
class PreparingDataForLocalDependenciesPipe
{
    /**
     * @param  LevyModel  $model
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(LevyModel $model, Closure $next) {

        if ($model->model_class && $model->model_table) {

            foreach ($model->model_relation_related_fields as $relation_name => $relation_info) {

                if (
                    $relation_info &&
                    isset($relation_info['local']) &&
                    $relation_info['local'] &&
                    $relation_info['local'] != 'id'
                ) {

                    if (isset($model->put_data[$relation_name])) {

                        $model->write_data[$relation_info['local']] =
                            \BlessModel::makeLevy($model->model_relations[$relation_name], $model->put_data[$relation_name]);

                        $model->write_data[$relation_info['local']]->relation_name = $relation_name;

                        unset($model->put_data[$relation_name]);

                    } else if ($model->nullable_fields[$relation_info['local']]) {

                        $model->write_data[$relation_info['local']] = null;
                    }
                }
            }
        }

        return $next($model);
    }
}
