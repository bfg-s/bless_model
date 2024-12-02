<?php

namespace Bfg\BlessModel\LevyModelPipes;

use Bfg\BlessModel\LevyModel;
use Closure;

/**
 * Class DefiningTableFieldsPipe
 * @package Bfg\BlessModel\LevyModelPipes
 */
class DefiningTableFieldsPipe
{
    /**
     * @var array
     */
    protected static $cache_fields = [];

    /**
     * @param  LevyModel  $model
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(LevyModel $model, Closure $next) {

        if ($model->model_class && $model->model_table) {

            if (isset(static::$cache_fields[$model->model_table])) {

                $model->nullable_fields = static::$cache_fields[$model->model_table]['nullable_fields'];

                $model->fields = static::$cache_fields[$model->model_table]['fields'];

            } else {

                $hasNullables = false;

                if (property_exists($model->src, 'nullables')) {

                    $result = $model->src->nullables;

                    if (is_array($result)) {

                        $model->nullable_fields = array_merge($model->nullable_fields, $result);

                        $hasNullables = true;
                    }
                }

                if (method_exists($model->src, 'nullables')) {

                    $result = $model->src->nullables();

                    if (is_array($result)) {

                        $model->nullable_fields = array_merge($model->nullable_fields, $result);

                        $hasNullables = true;
                    }
                }

                if (! $hasNullables && ! $model->nullable_fields) {

                    $fields = \DB::select(
                        "SELECT COL.COLUMN_NAME, COL.IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS COL WHERE COL.TABLE_NAME = '{$model->model_table}'"
                    );

                    foreach ($fields as $field) {

                        $model->nullable_fields[$field->COLUMN_NAME] = $field->IS_NULLABLE === 'YES';
                    }
                }

                static::$cache_fields[$model->model_table]['nullable_fields'] = $model->nullable_fields;

                static::$cache_fields[$model->model_table]['fields'] = $model->fields = array_merge(
                    array_keys($model->nullable_fields),
                    $model->src->getFillable(),
                );
            }
        }

        return $next($model);
    }
}
