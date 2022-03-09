<?php

namespace App\Http\Controllers\API\Organization;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailToOrgSubAdminJob;
use App\Models\Organization;
use App\Models\OrgDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrgSubAdminController extends Controller
{
    public function createOrgSubAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'mobile_no' => 'required|unique:users,mobile_no',
            'org' => 'required|numeric',
            'departmet_id' => 'numeric|nullable',
            'section_id' => 'numeric|nullable',
            'branch_id' => 'numeric|nullable',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        DB::beginTransaction();
        try {
            //Add User 
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->slug = User::userSlug($request->first_name, $request->last_name);
            $user->mobile_no = $request->mobile_no;
            $user->parent_id = Auth::user()->id;
            $user->password = bcrypt("Password@123");
            $user->role = Role::ORG_SUB_ADMIN;
            $user->save();

            // Add Org Details
            $org = new OrgDetail();
            $org->org_id = $request->org_id;
            $org->user_id = $user->id;
            $org->department_id = $request->department_id;
            $org->branch_id = $request->banrch_id;
            $org->section_id = $request->section_id;
            $org->save();
            $org = Organization::find($request->org_id);
            $details = [
                'description' => "You have to give Administrator access of $org->name and your user name $user->email email and default Password is Password@123",
                'name' => $user->first_name,
                'link' => config('app.ui_url')
            ];
            dispatch(new SendEmailToOrgSubAdminJob($details, $user->email));
            DB::commit();
            return response()->json(["message" => "Record Added Successfully."], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
