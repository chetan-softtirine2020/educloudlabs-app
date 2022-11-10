<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;
use App\Models\Role;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'mobile_no', 'role', 'parent_id', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function userSlug($first, $last)
    {
        $userCount = self::where('first_name', $first)->where('last_name', $last)->count();
        $userCount > 0 ? $count = $userCount : $count = "";
        return  Str::slug($first . " " . $last . "" . $count);
    }


    public static function getUserCode($role, $parentId)
    {
        $getCount = User::where('role', $role)->count();
        $getTotalUser = User::where('parent_id', $parentId)->count();
        $parentUser = User::find($parentId);
        $code = "";
        $parentCode = NULL;
        switch ($role) {
            case Role::LEARNING_PROVIDER:
                $number = sprintf('%05d', $getCount + 1);
                $code = "LP" . $number;
                break;

            case Role::USER:
                $number = sprintf('%05d', $getCount + 1);
                $code = "USR" . $number;
                break;

            case Role::ORG_SUB_ADMIN:
                $number = sprintf('%05d', $getTotalUser + 1);
                $code = $parentUser->name . "LP" . $number;
                $parentCode = $parentUser->name;
                break;

            case Role::PROVIDER_USER:
                $number = sprintf('%05d', $getTotalUser + 1);
                $code = $parentUser->name . "USR" . $number;
                $parentCode = $parentUser->name;
                break;

            case Role::ORGANIZATION:
                $number = sprintf('%05d', $getCount + 1);
                $code = "ORG" . $number;
                break;

            case Role::ORG_USER:
                $number = sprintf('%05d', $getTotalUser + 1);
                $code = $parentUser->name . "USR" . $number;
                $parentCode = $parentUser->name;
                break;
            default:
                break;
        }
        return  ['code' => $code, 'parent' => $parentCode];
    }
}
