<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    public $fillable = ['id', 'name', 'description', 'image', 'created'];
}
