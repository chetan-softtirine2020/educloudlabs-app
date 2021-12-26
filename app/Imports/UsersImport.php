<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\LPTUser;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Mail;
use App\Models\LPTraining;
use App\Mail\AddTrainingMail;
use Illuminate\Support\Facades\Validator;
class UsersImport implements ToCollection, WithHeadingRow
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
    public function collection(Collection $rows)
    {
        Validator::make($rows->toArray(), [
            '*.first_name' => 'required',
            '*.last_name' => 'required',
            '*.mobile_no' => 'required',
            '*.email' => 'required',          
        ])->validate();
     
        foreach ($rows as $row) {
            $checkUser = User::where('email', $row['email'])->first();
            if (!$checkUser) {
                User::create([
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'slug' => User::userSlug($row['first_name'], $row['last_name']),
                    'mobile_no' => $row['mobile_no'],
                    'parent_id' => Auth::user()->id,
                    'password' => bcrypt("Password@123"),
                    'role' => Role::PROVIDER_USER,
                ]);
            }
            $getUserId = User::where('email', $row['email'])->first();
            LPTUser::create([
                'user_id' => $getUserId->id,
                'training_id' => $this->training_id,
                'provider_id' => Auth::user()->id,
            ]);
            $training = LPTraining::where('id', $this->training_id)->first();
            Mail::to($row['email'])->send(new AddTrainingMail($training));
        }
    }
  
}
