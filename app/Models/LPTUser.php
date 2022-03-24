<?php

namespace App\Models;

use App\Models\BaseModel; 

class LPTUser extends BaseModel
{
    protected $fillable = ['user_id','provider_id','training_id'];
}
