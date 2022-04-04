<?php

namespace Bfg\BlessModel\Traits;

use Bfg\BlessModel\Facades\BlessModel;
use Illuminate\Contracts\Support\Arrayable;

trait Blessable
{
    public static function blessNew(Arrayable|string|array $data): mixed
    {
        return BlessModel::do(static::class, $data);
    }

    public function bless(Arrayable|string|array $data): mixed
    {
        return BlessModel::do($this, $data);
    }

    public static function onBlessSave(callable $callback): BlessModel
    {
        return BlessModel::on_save(static::class, $callback);
    }

    public static function onBlessSaved(callable $callback): BlessModel
    {
        return BlessModel::on_saved(static::class, $callback);
    }

    public static function onBlessCreate(callable $callback): BlessModel
    {
        return BlessModel::on_create(static::class, $callback);
    }

    public static function onBlessCreated(callable $callback): BlessModel
    {
        return BlessModel::on_created(static::class, $callback);
    }

    public static function onBlessUpdate(callable $callback): BlessModel
    {
        return BlessModel::on_update(static::class, $callback);
    }

    public static function onBlessUpdated(callable $callback): BlessModel
    {
        return BlessModel::on_updated(static::class, $callback);
    }

    public static function onBlessDelete(callable $callback): BlessModel
    {
        return BlessModel::on_delete(static::class, $callback);
    }

    public static function onBlessDeleted(callable $callback): BlessModel
    {
        return BlessModel::on_deleted(static::class, $callback);
    }

    public static function onBlessForceDelete(callable $callback): BlessModel
    {
        return BlessModel::on_force_delete(static::class, $callback);
    }

    public static function onBlessForceDeleted(callable $callback): BlessModel
    {
        return BlessModel::on_force_deleted(static::class, $callback);
    }

    public static function onBlessRestore(callable $callback): BlessModel
    {
        return BlessModel::on_restore(static::class, $callback);
    }

    public static function onBlessRestored(callable $callback): BlessModel
    {
        return BlessModel::on_restored(static::class, $callback);
    }
}
