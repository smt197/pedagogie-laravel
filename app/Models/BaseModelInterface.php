<?php

namespace App\Models;

interface BaseModelInterface
{
    public function create(array $attributes = []);
    public function update(array $attributes = [], array $options = []);
    public function delete();
    public static function find($id); 
}
