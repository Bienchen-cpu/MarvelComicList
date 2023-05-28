<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = ['characterId', 'name', 'description', 'thumbnail', 'resource_uri'];
    protected $table = 'characters';
}
