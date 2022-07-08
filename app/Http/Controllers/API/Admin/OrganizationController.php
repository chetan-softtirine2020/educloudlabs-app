<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\OrgApprovedEmail;
use Illuminate\Support\Facades\Mail;

class OrganizationController extends Controller
{
    public function getAllOrganizations()
    {
        try {
            $orgs = DB::select("SELECT * FROM organizations WHERE is_approved=?", [Organization::APPROVED]);
            $res['list'] = [];
            foreach ($orgs as $org) {
                $res['list'][] = [
                    "name" => $org->name,
                    "email" => $org->email,
                    "description" => $org->description,
                    "id" => $org->id,
                    'status'=>$org->status,
                    "is_approved" => $org->is_approved,
                ];
            }
            return response()->json($res, 200);
        } catch (Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getAllNewRegisterOrganizations()
    {
        try {
            $orgs = DB::select('SELECT * FROM organizations WHERE is_approved=? ORDER BY id DESC', [Organization::APPROVE]);
            $res['list'] = [];
            foreach ($orgs as $org) {
                $res['list'][] = [
                    "name" => $org->name,
                    "email" => $org->email,
                    "description" => $org->description,
                    "id" => $org->id,
                    "is_approved" => $org->is_approved,
                ];
            }
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function approvedOrganizationsConfirma()
    {
        try {
            $orgs = DB::select('SELECT * FROM organizations WHERE is_approved=?', [0]);
            $res['list'] = [];
            foreach ($orgs as $org) {
                $res['list'][] = [
                    "name" => $org->name,
                    "slug" => $org->slug,
                    "email" => $org->email,
                    "id" => $org->id,
                    "is_approved" => $org->is_approved,
                ];
            }
            return response()->json($res, 200);
        } catch (Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function approvedOrganization(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'org_id' => 'required',
            'is_approved' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $org = Organization::find($request->org_id);
            if ($request->is_approved == 1) {
                $org->status = 1;
            }
            $org->is_approved = $request->is_approved;
            $org->save();
            if ($request->is_approved == 1) {
                $user = new User();
                $user->first_name = $org->name;
                $user->last_name = $org->name;        
                $user->slug = Str::slug($org->name);
                $user->email = $org->email;
                $user->mobile_no = $org->mobile_no;
                $user->password = bcrypt("Password@123");
                $user->role = Role::ORGANIZATION;  
                $user->save();
                //Send Approved Email Data 
                $details['message'] = "Your organization register request has been approved please login your register email and default password is"." ".Str::random(8);
            } else {
                //Send Not Approved Email Data 
                $details['message'] = "Your organization register request  not approved so  contact to Educloudlabs admin on admin@educloudlabs.com";
            }
            $details['name'] = $org->name;
            Mail::to($org->email)->send(new OrgApprovedEmail($details));
            return response()->json(["message" => "Record approved Successfully."], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
