<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LPTUser extends Model
{
    protected $fillable = ['user_id','provider_id','training_id'];
}
