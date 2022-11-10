<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\BaseModel;
use App\Models\Organization;
use App\Models\OrgDetail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|unique:users,mobile_no|numeric',
            'password' => 'required|min:6',
            'user_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            DB::beginTransaction();
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->slug = User::userSlug($request->first_name, $request->last_name);
            $user->email = $request->email;
            $user->mobile_no = $request->mobile_no;
            $user->password = bcrypt($request->password);
            $user->role = $request->user_type == 6 ? Role::ORG_SUB_ADMIN : $request->user_type;
            if ($request->user_type == Role::LEARNING_PROVIDER) {
                $user->is_parent = 1;
                $user->parent_id =  Role::ADMIN;
                $user->status = 0;
            }
            if ($request->user_type == 6) {
                $checkOrgUser = Organization::find($request->org_id);
                $checkParentUser = User::where('email', $checkOrgUser->email)->first();
                $user->is_parent = 1;
                $user->parent_id = $checkParentUser->id;
                $user->status = 0;
            }
            $codes = User::getUserCode($request->user_type == 6 ? 7 : $request->user_type, $user->parent_id);
            $user->name = $codes['code'];
            $user->parent_name = $codes['parent'];
            $user->save();

            if ($request->user_type == 6) {
                $org = new OrgDetail();
                $org->user_id = $user->id;
                $org->org_id = $request->org_id;
                $org->department_id = $request->lob_id;
                $org->status = OrgDetail::INACTIVE;
                $org->save();
            }

            switch ($request->user_type) {
                case Role::USER:
                    $role = "user";
                    break;
                case Role::LEARNING_PROVIDER:
                    $role = "provider";
                    break;
                case Role::PROVIDER_USER:
                    $role = "provider_user";
                    break;
                case Role::ORGANIZATION:
                    $role = "organization";
                    break;
                case Role::ORG_SUB_ADMIN:
                    $role = "organization_user";
                    break;
                case Role::ORG_USER:
                    $role = "organization_user";
                    break;
                default:
                    $role = "admin";
                    break;
            }
            // $token = $user->createToken('MyApp')->accessToken;
            $success['success'] = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'mobile_no' => $user->mobile_no,
                'slug' => $user->sulg,
                '_id' => $user->id,
                'email' => $user->email,
                'roles' => [$role],
                'role' => $role,
                'token' => ""
            ];
            // $user->is_login = 1;
            //  $user->current_token = $token;
            DB::commit();
            return response()->json($success, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response(['message' => $e->getMessage()], 500);
            //return response()->json();7
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'email' =>  'required|email|exists:users,email',
                'password' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $checkLoginUser = User::where('email', $request->email)->first();
            // if account not approved by admin 
            if ($checkLoginUser && $checkLoginUser->status == BaseModel::INACTIVE) {
                return response()->json(['message' => "Your account not approved yet please contact to educloudlabs."], 403);
            }
            if ($request->is_previous && $checkLoginUser) {
                $checkLoginUser->is_login = 0;
                $checkLoginUser->current_token = null;
                $checkLoginUser->save();
            }
            if ($checkLoginUser && $checkLoginUser->is_login == 1) {
                return response()->json(['message' => "Your Account already login into another device or tab"], 403);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $authenticated_user = Auth::user();
                $user = User::find($authenticated_user->id);
                $token = $user->createToken('myApp')->accessToken;
                if ($user->role == Role::ORG_SUB_ADMIN) {
                    $role = Role::find(Role::ORGANIZATION);
                } else {
                    $role = Role::find($user->role);
                }
                $success['success'] = [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    '_id' => $user->id,
                    'slug' => $user->sulg,
                    'email' => $user->email,
                    'mobile_no' => $user->mobile_no,
                    'roles' => [$role->name],
                    'role' => $role->name,
                    'token' => $token
                ];
                $user->is_login = 1;
                $user->current_token = $token;
                $user->save();
                return response()->json($success, 200);
                //return $this->sendResponse($success, 'User login successfully.');
            } else {
                return response()->json(['message' => "Email or Password incorrect"], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'token' =>  'required',
            ]
        );
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            User::where('id', Auth::user()->id)->update(['is_login' => 0, 'current_token' => null]);
            $request->user()->token()->revoke();
            return response()->json(['message' => 'You have been successfully logged out!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getCurrentToken(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' =>  'required',
            ]
        );
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $user = User::where('id', Auth::user()->id)->first();
            return response()->json($user->current_token, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'current_password' => 'required',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $currentPassword = Auth::user()->password;
            if (!Hash::check($request->current_password, $currentPassword)) {
                return response()->json(['message' => "Current password is incorrect"], 403);
            }
            $user = User::find(Auth::user()->id);
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json(['message' => 'Your passwrod has been change successfully.'], 202);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mobile_no' => 'required|numeric|min:10',
            ]
        );
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $mobopt = random_int(100000, 999999);
            //$message = urlencode("Dear Customer, OTP is $mobopt for mobile no verify. EDUCLOUDLABS");
            $message = urlencode("Dear Customer, OTP is $mobopt for panel creation. RNDSMS");
            $authkey = "MWJlYjg4ZTFmZDF"; // Go to your Roundsms panel to get your authkey
            $sender_id = "RNDSMS";
            $type = 1;  //
            $route = 2; //
            $number =  $request->mobile_no;
            // $number = 797730;
            $send = "http://roundsms.com/api/sendhttp.php?authkey=" . $authkey . "&mobiles=" . $number . "&message=" . $message . "&sender=" . $sender_id . "&type=" . $type . "&route=" . $route;
            $res = file_get_contents($send);
            // if ($res) {
            //     return $res;
            // } else {
            //     return false;
            // }
            //return response()->json(['opt' => json_encode($mobopt)], 200);
            //info($mobopt);
            return response()->json(['otp' => base64_encode($mobopt), 'res' => $res], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
