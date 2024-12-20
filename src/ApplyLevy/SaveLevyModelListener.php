<?php

namespace Bfg\BlessModel\ApplyLevy;

use Bfg\BlessModel\Facades\BlessModel;
use Bfg\BlessModel\LevyModel;
use Bfg\BlessModel\LevyModelCollection;
use Illuminate\Support\Collection;

/**
 * Class SaveLevyModelListener
 * @package Bfg\BlessModel\ApplyLevy
 */
class SaveLevyModelListener
{
    /**
     * @param  LevyModel  $model
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation|mixed|string|null
     * @throws \Exception
     */
    public function handle(LevyModel $model)
    {
        if ($model->model_delete) {

            BlessModel::call_on('delete', $model->model_class, $model);

            $model->model_class->delete();

            BlessModel::call_on('deleted', $model->model_class, $model);

            return $model->model_class;

        } else if ($model->model_force_delete) {

            BlessModel::call_on('force_delete', $model->model_class, $model);

            if ($model->model_soft_delete) {

                $model->model_class->forceDelete();

            } else {

                $model->model_class->delete();
            }

            BlessModel::call_on('force_deleted', $model->model_class, $model);

            return $model->model_class;

        } else if ($model->model_restore) {

            if ($model->model_soft_delete) {

                BlessModel::call_on('restore', $model->model_class, $model);

                $model->model_class->restore();

                BlessModel::call_on('restored', $model->model_class, $model);
            }

            return $model->model_class;
        }

        $write_data = $model->write_data;

        foreach ($write_data as $key => $write) {

            if (is_object($write) && $write instanceof LevyModel) {

                if ($write->relation_name) {

                    if (!$model->model_exists) {
                        $src = $model->model_class->{$write->relation_name}();
                        $write->src = $src;
                    } else {
                        $src = $model->model_class->{$write->relation_name};
                        if (!$src) {
                            $write->src = $model->model_class->{$write->relation_name}();
                        } else {
                            $write->model_class = $src;
                            $write->model_exists = true;
                        }
                    }

                    $result = BlessModel::applyLevy($write);

                    $write_data[$key] = $result->{$write->model_key};

                } else {

                    unset($write_data[$key]);
                }
            }
        }

        if ($model->model_exists) {

            if (count($write_data)) {

                BlessModel::call_on('save', $model->model_class, $model);
                BlessModel::call_on('update', $model->model_class, $model);

                $model->model_class->update($write_data);

                BlessModel::call_on('saved', $model->model_class, $model);
                BlessModel::call_on('updated', $model->model_class, $model);
            }

            $model_result = $model->model_class;

        } else {

            if (count($write_data)) {

                BlessModel::call_on('save', $model->model_class, $model);
                BlessModel::call_on('create', $model->model_class, $model);

                $model_result = $model->src->create($write_data);

                BlessModel::call_on('saved', $model->model_class, $model);
                BlessModel::call_on('created', $model->model_class, $model);
            }
            else {

                $model_result = $model->model_class;
            }
        }

        foreach ($model->write_relations_data as $relation => $write_relations_data) {

            if ($write_relations_data instanceof LevyModelCollection) {

                $src = $model_result->{$relation}();

                $write_relations_data->map(function (LevyModel $model) use ($src) {
                    $model->src = $src;
                    return $model;
                });

            } else if ($write_relations_data instanceof LevyModel) {

                if ($model->model_exists) {
                    $src = $model_result->{$relation};
                    if (!(!$src || $src instanceof Collection)) {
                        $write_relations_data->model_class = $src;
                        $write_relations_data->model_exists = true;
                    }
                }
                $write_relations_data->src = $model_result->{$relation}();
            }

            BlessModel::applyLevy($write_relations_data);
        }

        return $model_result;
    }
}
