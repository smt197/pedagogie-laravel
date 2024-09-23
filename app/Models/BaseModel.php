<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Authenticatable implements BaseModelInterface
{
    public function create(array $attributes = [])
    {
        return static::query()->create($attributes);
    }

    public function update(array $attributes = [], array $options = [])
    {
        return parent::update($attributes, $options);
    }

    public function delete()
    {
        return parent::delete();
    }

    public static function find($id)
    {
        return static::query()->find($id);
    }

}
