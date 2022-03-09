<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    const ACTIVE = 1;
    const INACTIVE = 0;
    const APPROVED = 1;
    const APPROVE = 0;
   //Traning 
    const REGISTER=1;
    const START=2;
    const COMPLETED=3;
    }
