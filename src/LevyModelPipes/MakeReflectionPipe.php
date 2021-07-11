<?php

namespace Bfg\BlessModel\LevyModelPipes;

use Bfg\BlessModel\LevyModel;
use Closure;
use ReflectionClass;

/**
 * Class MakeReflectionPipe
 * @package Bfg\BlessModel\LevyModelPipes
 */
class MakeReflectionPipe
{
    /**
     * @param  LevyModel  $model
     * @param  Closure  $next
     * @return mixed
     * @throws \ReflectionException
     */
    public function handle(LevyModel $model, Closure $next) {

        if ($model->model_class) {

            $model->reflection = new ReflectionClass($model->model_class);
        }

        return $next($model);
    }
}
