<?php

namespace Bfg\BlessModel\LevyModelPipes;

use Bfg\BlessModel\LevyModel;
use Closure;

/**
 * Class ModelDefinitionPipe
 * @package Bfg\BlessModel\LevyModelPipes
 */
class ModelDefinitionPipe
{
    /**
     * @param  LevyModel  $model
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(LevyModel $model, Closure $next) {

        if (
            $model->model_class &&
            $model->model_table &&
            $model->model_key
        ) {

            if (
                isset($model->put_data[$model->model_key]) &&
                $model->put_data[$model->model_key]
            ) {
                $this->getModel($model, $model->put_data[$model->model_key]);
                unset($model->put_data[$model->model_key]);
            }

            else if (
                isset($model->put_data['delete']) &&
                $model->put_data['delete']
            ) {
                if ($this->getModel($model, $model->put_data['delete'])) {
                    $model->model_delete = true;
                }
                unset($model->put_data['delete']);
            }

            else if (
                isset($model->put_data['force_delete']) &&
                $model->put_data['force_delete']
            ) {
                if ($this->getModel($model, $model->put_data['force_delete'])) {
                    $model->model_force_delete = true;
                }
                unset($model->put_data['force_delete']);
            }

            else if (
                isset($model->put_data['restore']) &&
                $model->put_data['restore']
            ) {
                if ($this->getModel($model, $model->put_data['restore'])) {
                    $model->model_restore = true;
                }
                unset($model->put_data['restore']);
            }
        }

        return $next($model);
    }

    /**
     * @param  LevyModel  $model
     * @param $id
     * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Model[]|mixed|null
     */
    protected function getModel(LevyModel $model, $id): mixed
    {
        $result = $model->model_soft_delete ?
            $model->model_class->withTrashed()->find($id) :
            $model->model_class->find($id);

        if ($result) {
            $model->model_class = $result;
            $model->model_exists = $result->exists;
        }

        return $result;
    }
}
