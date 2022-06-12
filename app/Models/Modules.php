<?php


namespace App\Models;

use App\Models\BaseModel;

class Modules extends BaseModel
{
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
