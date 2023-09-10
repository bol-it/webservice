<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class results_web_forms extends Model
{
    use HasFactory;

    protected $table = 'results_web_forms';
    protected $primaryKey='id';
    public $timestamps = true;
}
