<?php

namespace Bfg\BlessModel\LevyModelPipes;

use Bfg\BlessModel\LevyModel;
use Closure;

/**
 * Class PreparingDataForWritingPipe
 * @package Bfg\BlessModel\LevyModelPipes
 */
class PreparingDataForWritingPipe
{
    /**
     * @param  LevyModel  $model
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(LevyModel $model, Closure $next) {

        if ($model->model_class && $model->model_table) {

            $model->input_keys = array_keys($model->put_data);

            foreach ($model->fields as $field) {
                if (array_key_exists($field, $model->put_data)) {
                    if ($model->put_data[$field] !== '') {
                        $model->write_data[$field] = $model->put_data[$field];
                    } else if (isset($model->nullable_fields[$field]) && $model->nullable_fields[$field]) {
                        $model->write_data[$field] = null;
                    } else {
                        $model->write_data[$field] = $model->put_data[$field];
                    }
                    unset($model->put_data[$field]);
                }
            }
        }

        return $next($model);
    }
}
