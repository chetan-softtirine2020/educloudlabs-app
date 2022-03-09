<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ADMIN = 1;
    const USER = 2;
    const LEARNING_PROVIDER = 3;
    const ORGANIZATION = 4;
    const PROVIDER_USER = 5;
    const ORG_USER = 6;
    const ORG_SUB_ADMIN = 7;
}
