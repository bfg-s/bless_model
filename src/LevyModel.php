<?php

namespace Bfg\BlessModel;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;

/**
 * Class LevyModel
 * @package Bfg\BlessModel
 */
class LevyModel
{
    /**
     * Model db table
     *
     * @var string|null
     */
    public ?string $model_table;

    /**
     * Model class namespace
     *
     * @var string|null
     */
    public ?string $model_class_name;

    /**
     * Specific Model
     *
     * @var Model|mixed
     */
    public mixed $model_class;

    /**
     * Model key name
     *
     * @var string|null
     */
    public ?string $model_key;

    /**
     * If related levy, the name of this relation
     *
     * @var string|null
     */
    public ?string $relation_name = null;

    /**
     * Indicates if the model exists
     *
     * @var bool
     */
    public bool $model_exists = false;

    /**
     * Indicates if the model need delete
     *
     * @var bool
     */
    public bool $model_delete = false;

    /**
     * Indicates if the model need a force delete
     *
     * @var bool
     */
    public bool $model_force_delete = false;

    /**
     * Indicates if the model need restore
     *
     * @var bool
     */
    public bool $model_restore = false;

    /**
     * Indicates if the model need restore
     *
     * @var bool
     */
    public bool $model_soft_delete = false;

    /**
     * Model relation list
     *
     * @var array
     */
    public array $model_relations = [];

    /**
     * Model relation name list
     *
     * @var array
     */
    public array $model_relation_names = [];

    /**
     * Model relation related fields
     *
     * @var array
     */
    public array $model_relation_related_fields = [];

    /**
     * Source for work
     *
     * @var Model|Relation|Builder|mixed|null
     */
    public Model|Relation|Builder|null $src;

    /**
     * Model reflection
     *
     * @var ReflectionClass|null
     */
    public ?ReflectionClass $reflection;

    /**
     * Table fields
     *
     * @var array
     */
    public array $fields = [];

    /**
     * Table nullable fields
     *
     * @var array
     */
    public array $nullable_fields = [];

    /**
     * All user data for before write
     *
     * @var \Illuminate\Contracts\Support\Arrayable|array
     */
    public Arrayable|array $put_data = [];

    /**
     * Data for writing
     *
     * @var array
     */
    public array $write_data = [];

    /**
     * Data for relations writing
     *
     * @var array
     */
    public array $write_relations_data = [];

    /**
     * LevyModel constructor.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Builder|string  $src
     * @param  \Illuminate\Contracts\Support\Arrayable|string|array  $put_data
     */
    public function __construct(
        Model|Relation|Builder|string $src,
        Arrayable|string|array $put_data
    ) {

        $this->src = is_string($src) ? new $src : $src;

        if (is_string($put_data)) {

            $put_data = new $put_data;
        }

        $this->put_data = $put_data instanceof Arrayable ? $put_data->toArray() : (array)$put_data;
    }
}
