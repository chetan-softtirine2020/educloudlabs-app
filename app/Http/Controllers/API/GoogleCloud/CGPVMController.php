<?php

namespace App\Http\Controllers\API\GoogleCloud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\VMTrainingInvitationImport;
use App\Models\GCUser;
use App\Models\Role;
use App\Models\User;
use App\Models\VMDetails;
use App\Models\VMUsed;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\VMTrainingInvitationJob;

class CGPVMController extends Controller
{

    public function getVmCount()
    {
        try {
            $vmUserId = GCUser::where("user_id", Auth::user()->id)->orderBy('id', 'DESC')->first();
            $count = VMDetails::where('user_id', $vmUserId->id)->where('is_assign', 0)->count();
            return response($count, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function importVMTrainingUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $file = $request->file('file');
            $import = new VMTrainingInvitationImport();
            $import->import($file);
            if ($import->errors()->isNotEmpty()) {
                return response()->json(['message' => "Something went worng"], 500);
            }
            if ($import->failures()->isNotEmpty()) {
                return response($import->failures(), 422);
                //info($import->failures());
            }
            return response()->json(['message' => "Record added suceessfully"], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function addVMTrainingUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|regex:/^([a-zA-Z]+)(\s[a-zA-Z]+)*$/',
            'last_name' => 'required|regex:/^([a-zA-Z]+)(\s[a-zA-Z]+)*$/',
            'mobile_no' => 'required|numeric|min:10',
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $checkUser = User::where('email', $request->email)->first();
            $password = Str::random(8);
            $gcUser = GCUser::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->first();
            $vmDetails = VMDetails::where('is_assign', 0)->where('user_id', $gcUser->id)->first();
            if (!$checkUser) {
                $slug = User::userSlug($request->first_name, $request->last_name);
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->lastname,
                    'email' => $request->email,
                    'slug' => $slug,
                    'mobile_no' => $request->mobile_no,
                    'parent_id' => Auth::user()->id,
                    'password' => bcrypt($password),
                    'role' => Auth::user()->role == Role::LEARNING_PROVIDER ? Role::PROVIDER_USER : Role::ORG_USER
                ]);
                $user->slug = $slug;
                $user->save();
            }
            $assignVm = new VMUsed();
            $assignVm->assign_user_id = $checkUser ? $checkUser->id : $user->id;
            $assignVm->vm_id = $vmDetails->id;
            $assignVm->assign_by = Auth::user()->id;
            $assignVm->save();
            VMDetails::where('id', $vmDetails->id)->update(['is_assign' => 1]);
            $link = "https://educloudlabs.com/vm/" . $vmDetails->vm_name;
            $otherText = !$checkUser ? "Use your register email and  default password for the login your account " . $password : " ";
            $description = "Login your details and use virtual machine for training";
            $details['user_name'] = $$request->first_name;
            $details['link'] = $link;
            $details['description'] = $description . " " . $otherText;
            dispatch(new VMTrainingInvitationJob($details, $request->email));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getAssignVMDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $result = DB::select("SELECT u.first_name,u.last_name,u.id as u_id,vu.vm_start,vu.vm_stop,vu.used_min,vu.status,vu.cost FROM v_m_useds vu JOIN users u ON vu.assign_user_id=u.id JOIN v_m_details as vd ON vd.id=vu.vm_id WHERE vd.vm_name=?", [$request->name]);
            $res['list'] = $result;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
