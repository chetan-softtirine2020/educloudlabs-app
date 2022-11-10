<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrgUserRequestStatusJob;
use App\Models\OrgDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    public function getUserList(Request $request)
    {
        try {

            $users = DB::select("SELECT first_name,last_name,email,mobile_no,id,slug FROM  users WHERE role=? AND status=?", [$request->role, $request->type]);
            $res['list'] = $users;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function getUserChildUser(Request $request)
    {
        try {
            $user = User::where('slug', $request->slug)->first();
            $users = DB::select('SELECT first_name,last_name,email,mobile_no,id,slug FROM users WHERE parent_id=?', [$user->id]);
            $res['list'] = $users;
            return response()->json($res, 200);
        } catch (Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getOrgRequestUser(Request $request)
    {
        try {
            $users = DB::select("SELECT u.id AS usid,u.email,u.first_name,u.last_name,o.name As oname,d.name as lob, od.id as odid 
        FROM org_details AS od 
        JOIN users As u ON u.id=od.user_id 
        JOIN departments As d ON d.id=od.department_id 
        JOIN organizations As o ON o.id=od.org_id 
        WHERE od.status=? 
        ", [$request->type]);
            $res['list'] = $users;
            return response()->json($res, 200);
        } catch (Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function updateOrgRequestUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'odid' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $org = OrgDetail::find($request->odid);
            $user = User::find($org->user_id);
            $msg = "";
            $details = [];
            $details['name'] = $user->first_name . " " . $user->last_name;
            if ($request->status == OrgDetail::ACCEPT) {
                $user->status = OrgDetail::ACCEPT;
                $org->status = OrgDetail::ACCEPT;
                $msg = "Request is accepted";
                //Send Email Active 
                $details['message'] = "Your Educloudlabs request is accepted you can login your account with register email and password.";
            } else {
                $user->status = OrgDetail::REJECT;
                $org->status = OrgDetail::REJECT;
                $msg = "Request is not accepted";
                //Send Email IN Active 
                $details['message'] = "Your Educloudlabs request is rejected.";
            }
            $org->save();
            $user->save();
            dispatch(new SendOrgUserRequestStatusJob($details, $user->email));
            return response()->json(["message" => $msg], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function updateLearningProviderRequestUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {

            $user = User::find($request->id);
            $msg = "";
            $details = [];
            $details['name'] = $user->first_name . " " . $user->last_name;
            if ($request->status == 1) {
                //1 for accept
                $user->status = 1;
                $msg = "Request is accepted";
                //Send Email Active 
                $details['message'] = "Your Educloudlabs request is accepted you can login your account with register email and password.";
            } else {
                //2 for reject
                $user->status = 2;
                $msg = "Request is not accepted";
                //Send Email IN Active 
                $details['message'] = "Your Educloudlabs account request is rejected.";
            }
            $user->save();
            dispatch(new SendOrgUserRequestStatusJob($details, $user->email));
            return response()->json(["message" => $msg], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
