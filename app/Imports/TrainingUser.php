<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Role;
use App\Models\LPTUser;
use App\Models\LPTraining;
use App\Mail\AddTrainingMail;
use App\Jobs\AddLPTrainingUserJob;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class TrainingUser implements
    ToCollection,
    WithHeadingRow,
    SkipsOnError,
    WithValidation,
    SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    public function __construct($traning_id)
    {
        $this->training_id = $traning_id;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
      
        foreach ($rows as $row) {
            $checkUser = User::where('email', $row['email'])->first();
            $password = Str::random(8);
            if (!$checkUser) {
                $slug = User::userSlug($row["firstname"], $row["lastname"]);
                $user = User::create([
                    'first_name' => $row["firstname"],
                    'last_name' => $row["lastname"],
                    'email' => $row["email"],
                    'slug' => $slug,
                    'mobile_no' => $row["mobileno"],
                    'parent_id' => Auth::user()->id,
                    'password' => bcrypt($password),
                    'role' => Auth::user()->role == Role::LEARNING_PROVIDER ? Role::PROVIDER_USER : Role::ORG_USER
                ]);
                $user->slug = $slug;
                $user->save();
            }
            $checkTraining = LPTUser::where('training_id', $this->training_id)->where('user_id', $checkUser ? $checkUser->id : $user->id)->first();
            if (!$checkTraining) {
                LPTUser::create([
                    'user_id' => $checkUser ? $checkUser->id : $user->id,
                    'training_id' => $this->training_id,
                    'provider_id' => Auth::user()->id,
                ]);
                $training = LPTraining::where('id', $this->training_id)->first();
                $link = "https://educloudlabs.com/training/" . $training->slug;
                $otherText = !$checkUser ? "Use your default password for the login your account " . $password : " ";
                $description = $training['description'];
                $details['name'] = $training->name;
                $details['user_name'] = $row['firstname'];
                $details['link'] = $link;
                $details['description'] = $description . " " . $otherText;
                dispatch(new AddLPTrainingUserJob($details, $row["email"]));
            }
        }
    }
    public function rules(): array
    {
        return [
            "*.firstname" => ['required'],
            "*.lastname" => ['required'],
            "*.email" => ['required', 'email'],
            "*.mobileno" => ['required']
        ];
    }
}
