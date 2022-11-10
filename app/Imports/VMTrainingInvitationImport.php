<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Role;
use App\Jobs\VMTrainingInvitationJob;
use App\Models\GCUser;
use App\Models\VMDetails;
use App\Models\VMUsed;
use Illuminate\Support\Str;

class VMTrainingInvitationImport implements
    ToCollection,
    WithHeadingRow,
    SkipsOnError,
    WithValidation,
    SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    public function __construct()
    {
        //  $this->training_id = $traning_id;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {


        $gcUser = GCUser::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->first();
        $vmDetails = VMDetails::where('is_assign', 0)->where('user_id', $gcUser->id)->get();
        $availableVm = count($vmDetails);
        foreach ($rows as $key => $row) {
            $keyNo = $key + 1;
            if ($keyNo <= $availableVm) {
                $checkUser = User::where('email', $row['email'])->first();
                $password = Str::random(8);
                if (!$checkUser) {
                    $slug = User::userSlug($row["firstname"], $row["lastname"]);
                    $codes = User::getUserCode(Auth::user()->role == Role::LEARNING_PROVIDER ? Role::PROVIDER_USER : Role::ORG_USER, Auth::user()->id);
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
                    $user->name = $codes['code'];
                    $user->parent_name = $codes['parent'];
                    $user->save();
                }
                //   $checkTraining = VMUsed::where('training_id', $this->training_id)->where('user_id', $checkUser ? $checkUser->id : $user->id)->first();
                //if (!$checkTraining) {
                $assignVm = new VMUsed();
                $assignVm->assign_user_id = $checkUser ? $checkUser->id : $user->id;
                $assignVm->vm_id = $vmDetails[$key]->id;
                $assignVm->assign_by = Auth::user()->id;
                $assignVm->save();
                VMDetails::where('id', $vmDetails[$key]->id)->update(['is_assign' => 1]);


                $link = "https://educloudlabs.com/vm/" . $vmDetails[$key]->vm_name;
                $otherText = !$checkUser ? "Use your register email and  default password for the login your account " . $password : " ";
                $description = "Login your details and use virtual machine for training";
                $details['user_name'] = $row['firstname'];
                $details['link'] = $link;
                $details['description'] = $description . " " . $otherText;
                dispatch(new VMTrainingInvitationJob($details, $row["email"]));
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
