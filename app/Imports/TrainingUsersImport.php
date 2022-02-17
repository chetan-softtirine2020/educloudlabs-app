<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class TrainingUsersImport implements ToModel, SkipsOnError,WithHeadingRow,WithValidation
{
    use Importable, SkipsErrors;
    public function __construct($traning_id)
    {
        $this->training_id = $traning_id;
        //$this->users = User::select("id", "email")->get();
    }
    /**
     * @param Collection $collection
     */

    public function model(array $row)
    {
        return new User([
                'first_name' => $row['first'],
                'last_name' => $row['last'],
                'email' => $row['email'],
                'slug' => User::userSlug($row['first'], $row['last']),
                'mobile_no' => $row['mobile'],
                'parent_id' =>1,
                'password' => bcrypt("Password@123"),
                'role' => Role::PROVIDER_USER
        ]);
    }


     // public function collection(Collection $rows)
    // {
    //       info("here");
    //       info($rows);



    //     foreach ($rows as $row) {
    //         // $checkUser = User::where('email', $row['email'])->first();
    //         User::create([
    //             'first_name' => $row['first'],
    //             'last_name' => $row['last'],
    //             'email' => $row['email'],
    //             'slug' => User::userSlug($row['first'], $row['last']),
    //             'mobile_no' => $row['mobile'],
    //             'parent_id' => Auth::user()->id,
    //             'password' => bcrypt("Password@123"),
    //             'role' => Role::PROVIDER_USER
    //         ]);
    //         //  if (!$checkUser) {
    //         //     User::create([
    //         //         'first_name' => $row['first'],
    //         //         'last_name' => $row['last'],
    //         //         'email' => $row['email'],
    //         //         'slug' => User::userSlug($row['first'], $row['last']),
    //         //         'mobile_no' => $row['mobile'],
    //         //         'parent_id' => Auth::user()->id,
    //         //         'password' => bcrypt("Password@123"),
    //         //         'role' => Role::PROVIDER_USER,
    //         //     ]);
    //         // }

    //     }
  // }
    public function rules(): array
    {
        return [
              '*.first'=>['required'],
              '*.last'=>['required'],
              '*.mobile'=>['required'],
              '*.email'=>['required'],            
        ];
    }
    public function onError(Throwable $error)
    {
    }
    // public function startRow(): int 
    // {
    //      return 1;
    // }

    // public function batchSize(): int
    // {
    //     return 500;
    // }

    // public function chunkSize(): int
    // {
    //     return 500;
    // }
}
