<?php

namespace App\Imports;

use App\Models\User;
use App\Models\LPTUser;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class LPUsersImport implements ToModel, WithHeadingRow
{
    public function __construct($traning_id)
    {
        $this->training_id = $traning_id;
        $this->users = User::select("id", "email")->get();
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $user = User::where("email", $row['email'])->first();
        if ($user) {
            return new User([
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'mobile_no' => $row['mobile_no'],
                'parent_id' => Auth::user()->id,
                'password' => bcrypt("Password@123"),
                'role' => Role::PROVIDER_USER,
            ]);
        }
        $lpuser = User::where("email", $row['email'])->first();
        if ($lpuser) {
            $lptuser = new LPTUser();
            $lptuser->user_id = $user->id;
            $lptuser->training_id =$this->training_id;
            $lptuser->provider_id = Auth::user()->id;
            $lptuser->save();
        }
    }
}
