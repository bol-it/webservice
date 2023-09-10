<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class web_forms extends Model
{
    use SoftDeletes;

    protected $table = 'web_forms';
    protected $primaryKey='id';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}