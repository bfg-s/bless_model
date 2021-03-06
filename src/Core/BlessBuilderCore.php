<?php

namespace Bfg\BlessModel\Core;

use Bfg\BlessModel\LevyModel;
use Bfg\BlessModel\LevyModelCollection;
use Bfg\BlessModel\LevyModelPipes\DefiningTableFieldsPipe;
use Bfg\BlessModel\LevyModelPipes\ModelDefinitionPipe;
use Bfg\BlessModel\LevyModelPipes\MakeReflectionPipe;
use Bfg\BlessModel\LevyModelPipes\GeneralModelInformationPipe;
use Bfg\BlessModel\LevyModelPipes\PreparingDataForWritingPipe;
use Bfg\BlessModel\LevyModelPipes\PreparingDataForLocalDependenciesPipe;
use Bfg\BlessModel\LevyModelPipes\DefiningModelDependenciesPipe;
use Bfg\BlessModel\LevyModelPipes\PreparingDataForWritingDependenciesPipe;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pipeline\Pipeline;

/**
 * Class BlessBuilderCore
 */
class BlessBuilderCore extends EventCore
{
    /**
     * BlessModel constructor.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(
        protected Container $container
    ) {}

    /**
     * @param  Model|Relation|Builder|string  $model
     * @param  Arrayable|string|array  $data
     * @return LevyModel
     */
    public function makeLevy(
        Model|Relation|Builder|string $model,
        Arrayable|string|array $data
    ): LevyModel {
        return (new Pipeline($this->container))
            ->send(new LevyModel($model, $data))
            ->through([
                GeneralModelInformationPipe::class,
                ModelDefinitionPipe::class,
                MakeReflectionPipe::class,
                DefiningModelDependenciesPipe::class,
                DefiningTableFieldsPipe::class,
                PreparingDataForWritingPipe::class,
                PreparingDataForLocalDependenciesPipe::class,
                PreparingDataForWritingDependenciesPipe::class,
            ])->thenReturn();
    }

    /**
     * @param  Model|Relation|Builder|string  $model
     * @param  Arrayable|string|array  $data
     * @return LevyModelCollection|LevyModel
     */
    public function makeLevyCollection(
        Model|Relation|Builder|string $model,
        Arrayable|string|array $data
    ): LevyModelCollection|LevyModel {

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        return is_assoc($data)
            ? $this->makeLevy($model, $data)
            : (new LevyModelCollection($data))
                ->filter(fn ($i) => is_array($i))
                ->filter(fn ($i) => is_assoc($i))
                ->map(fn (array $d) => $this->makeLevy($model, $d));
    }

    /**
     *
     * @param  LevyModel|LevyModelCollection  $model
     * @return mixed
     */
    public function applyLevy(LevyModel|LevyModelCollection $model): mixed
    {
        return \Arr::first(event($model));
    }
}
