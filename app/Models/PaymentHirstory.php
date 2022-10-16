<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHirstory extends Model
{
    const SUCCESS = 1;
    const FAILED = 2;
    const PENDING = 3;
}
