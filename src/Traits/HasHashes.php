<?php

namespace Bastinald\LaravelAutomaticMigrations\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait HasHashes
{
    protected static function bootHasHashes()
    {
        static::saving(function ($model) {
            if (property_exists($model, 'hashes') && !empty($model->hashes)) {
                foreach (Arr::wrap($model->hashes) as $attribute) {
                    if (!empty($model->$attribute) &&
                        Str::length($model->$attribute) < 60 &&
                        !Str::startsWith($model->$attribute, '$2y$')) {
                        $model->$attribute = Hash::make($model->$attribute);
                    }
                }
            }
        });
    }
}
