<?php

namespace Bfg\BlessModel\LevyModelPipes;

use Bfg\BlessModel\LevyModel;
use Bfg\BlessModel\LevyModelCollection;
use Closure;

/**
 * Class PreparingDataForWritingDependenciesPipe
 * @package Bfg\BlessModel\LevyModelPipes
 */
class PreparingDataForWritingDependenciesPipe
{
    /**
     * @param  LevyModel  $model
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(LevyModel $model, Closure $next): mixed
    {
        if ($model->model_class && $model->model_table) {

            foreach ($model->model_relations as $name => $model_relation) {

                if (isset($model->put_data[$name])) {

                    if (is_assoc($model->put_data[$name])) {

                        $model->write_relations_data[$name] =
                            \BlessModel::makeLevy($model_relation, $model->put_data[$name]);

                    } else {

                        $model->write_relations_data[$name] =
                            (new LevyModelCollection($model->put_data[$name]))
                                ->filter(fn ($i) => is_array($i))
                                ->filter(fn ($i) => is_assoc($i))
                                ->map(fn (array $data) => \BlessModel::makeLevy($model_relation, $data));
                    }
                }
            }
        }

        return $next($model);
    }
}
