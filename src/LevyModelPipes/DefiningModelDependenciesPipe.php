<?php

namespace Bfg\BlessModel\LevyModelPipes;

use Bfg\BlessModel\LevyModel;
use Closure;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

/**
 * Class DefiningModelDependenciesPipe
 * @package Bfg\BlessModel\LevyModelPipes
 */
class DefiningModelDependenciesPipe
{
    /**
     * @var array
     */
    protected static $relation_cache = [];

    /**
     * @var string[]
     */
    protected $types = [
        'hasMany', 'hasManyThrough', 'hasOneThrough', 'belongsToMany', 'hasOne',
        'belongsTo', 'morphOne', 'morphTo', 'morphMany', 'morphToMany', 'morphedByMany',
    ];

    /**
     * @var array
     */
    protected array $model_lines = [];

    /**
     * @param  \Bfg\BlessModel\LevyModel  $model
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(LevyModel $model, Closure $next) {

        if ($model->model_class) {

            $this->model_lines = explode("\n", file_get_contents($model->reflection->getFileName()));

            $model->model_relations = $this->getAllRelations($model);

            $model->model_relation_names = array_map(
                fn ($i) => get_class($i->getRelated()),
                $model->model_relations
            );

            $model->model_relation_related_fields = array_map(
                function ($relation) {

                    if ($relation instanceof HasOneOrMany) {

                        return [
                            'local' => $relation->getLocalKeyName(),
                            'foreign' => $relation->getForeignKeyName(),
                        ];
                    }

                }, $model->model_relations
            );
        }

        return $next($model);
    }

    /**
     * Identify all relationships for a given model
     *
     * @param  \Bfg\BlessModel\LevyModel  $model
     * @return array|mixed
     */
    protected function getAllRelations(LevyModel $model): array
    {
        $cache_name = $model->model_class_name."_relations";

        if (isset(static::$relation_cache[$cache_name])) {

            return static::$relation_cache[$cache_name];
        }

        $traits = $model->reflection->getTraits();
        $traitMethodNames = [];
        foreach ($traits as $name => $trait) {
            $traitMethods = $trait->getMethods();
            foreach ($traitMethods as $traitMethod) {
                $traitMethodNames[] = $traitMethod->getName();
            }
        }

        $currentMethod = collect(explode('::', __METHOD__))->last();
        $methods = (array)$model->reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methods = collect($methods)->filter(function ($method) use ($model, $traitMethodNames, $currentMethod) {
            $methodName = $method->getName();
            if (!in_array($methodName, $traitMethodNames)
                && !str_starts_with($methodName, '__')
                && $method->class === $model->model_class_name
                && !$method->isStatic()
                && $methodName != $currentMethod
            ) {
                $r = new \ReflectionMethod($model->model_class_name, $methodName);
                $parameters = $r->getParameters();
                return collect($parameters)->filter(function ($parameter) {
                    return !$parameter->isOptional();
                })->isEmpty();
            }
            return false;
        })->mapWithKeys(function (\ReflectionMethod $method) use ($model) {
            $methodName = $method->getName();
            $model_content = $this->getMethodByLines($method->getStartLine(), $method->getEndLine());
            if (
                !preg_match('/^get([^;]+?)Attribute$/', $methodName) &&
                !preg_match('/^set([^;]+?)Attribute$/', $methodName) &&
                !preg_match('/^scope([^;]+?)$/', $methodName) &&
                preg_match('/return \$this->('.implode('|', $this->types).')\s*\(.*\)\s*;/', $model_content)
            ) {
                $relation = $model->model_class->$methodName();
                if (is_subclass_of($relation, \Illuminate\Database\Eloquent\Relations\Relation::class)) {
                    return [$methodName => $relation];
                }
            }
            return [];
        })->toArray();

        static::$relation_cache[$cache_name] = $methods;

        return $methods;
    }

    protected function getMethodByLines(int $start, int $end)
    {
        return implode("\n", array_slice($this->model_lines, $start-1, ($end-$start)+1));
    }
}
