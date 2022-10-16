<?php

namespace App\Models;

use App\Models\BaseModel;

class Topic extends BaseModel
{
    public function module()
    {
        return $this->belongsTo(Modules::class);
    }
}
