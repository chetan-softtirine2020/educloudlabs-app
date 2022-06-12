<?php


namespace App\Models;

use App\Models\BaseModel;


class Course extends BaseModel
{
    //
    public function modules()
    {
        return $this->hasMany(Modules::class);
    }
}
