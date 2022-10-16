<?php


namespace App\Models;

use App\Models\BaseModel;


class Course extends BaseModel
{

    //     public function modules(){
    //         return $this->hasMany(Modules::class);
    //     }

    //    public function modulesWithTopic(){
    //         return $this->modules()->with('module_id');
    //    }

    public $timestamps = false;

    public function modules()
    {
        return $this->hasMany(Modules::class);
    }

    public function topics()
    {
        return $this->hasManyThrough(Topic::class, Modules::class);
    }
}
