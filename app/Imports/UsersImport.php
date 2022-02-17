<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\LPTUser;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Mail;
use App\Models\LPTraining;
use App\Mail\AddTrainingMail;
use App\Jobs\AddLPTrainingUserJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\ValidationException;

class UsersImport implements ToModel, WithValidation, WithHeadingRow
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
        info($row);
        try {
            return new User([
                'first_name' => $row[1],
                'last_name' => $row['last'],
                'email' => $row['email'],
                'slug' => User::userSlug($row['first'], $row['last']),
                'mobile_no' => $row['mobile'],
                'parent_id' => 1,
                'password' => bcrypt("Password@123"),
                'role' => Role::PROVIDER_USER
            ]);
        } catch (ValidationException $e) {
            info($e);
        }
    }

    // public function collection(Collection $rows)
    // {
    //      info("call in excel");
    //     // Validator::make($rows->toArray(), [
    //     //     '*.first_name' => 'required',
    //     //     '*.last_name' => 'required',
    //     //     '*.mobile_no' => 'required',
    //     //     '*.email' => 'required',
    //     // ])->validate();

    //     foreach ($rows as $row) {
    //         $checkUser = User::where('email', $row['email'])->first();
    //          if (!$checkUser) {
    //             User::create([
    //                 'first_name' => $row['first_name'],
    //                 'last_name' => $row['last_name'],
    //                 'email' => $row['email'],
    //                 'slug' => User::userSlug($row['first_name'], $row['last_name']),
    //                 'mobile_no' => $row['mobile_no'],
    //                 'parent_id' => Auth::user()->id,
    //                 'password' => bcrypt("Password@123"),
    //                 'role' => Role::PROVIDER_USER,
    //             ]);
    //         }
    //         $getUserId = User::where('email', $row['email'])->first();            
    //         LPTUser::create([
    //             'user_id' => $getUserId->id,
    //             'training_id' => $this->training_id,
    //             'provider_id' => Auth::user()->id,
    //         ]);
    //         $training = LPTraining::where('id', $this->training_id)->first();
    //         $link = "https://educloudlabs.com/training/" . $training->slug;
    //         $training['link'] = $link;
    //         $training['password'] = Str::random(8);         
    //         //dispatch(new AddLPTrainingUserJob($training, $getUserId->email));
    //          Mail::to($row['email'])->send(new AddTrainingMail($training));
    //     }
    // }

    public function rules(): array
    {
        return [
            '*.first' => ['required'],
            '*.last' => ['required'],
            '*.mobile' => ['required'],
            '*.email' => ['required'],
        ];
    }
}
