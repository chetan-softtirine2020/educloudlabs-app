<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

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
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->slug = User::userSlug($request->first_name, $request->last_name);
            $user->email = $request->email;
            $user->mobile_no = $request->mobile_no;
            $user->password = bcrypt($request->password);
            $user->role = $request->user_type;
            if ($request->user_type == Role::LEARNING_PROVIDER || $request->user_type == Role::ORGANIZATION) {
                $user->is_parent = 1;
            }
            if ($request->type == 6) {
                $user->is_parent = $request->org_id;
            }
            $user->save();
            switch ($request->user_type) {
                case Role::USER:
                    $role = "user";
                    break;
                case Role::LEARNING_PROVIDER:
                    $role = "provider";
                    break;
                case Role::ORGANIZATION:
                    $role = "organization";
                    break;
                case Role::PROVIDER_USER:
                    $role = "provider_user";
                    break;
                case Role::ORG_USER:
                    $role = "organization_user";
                    break;
                default:
                    $role = "admin";
                    break;
            }
            $token = $user->createToken('MyApp')->accessToken;
            $success['success'] = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'mobile_no' => $user->mobile_no,
                'slug' => $user->sulg,
                '_id' => $user->id,
                'email' => $user->email,
                'roles' => [$role],
                'token' => $token
            ];
            return response()->json($success, 201);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
            //return response()->json();
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
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $authenticated_user = Auth::user();
                $user = User::find($authenticated_user->id);
                $token = $user->createToken('myApp')->accessToken;
                $role = Role::find($user->role);
                $success['success'] = [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    '_id' => $user->id,
                    'slug' => $user->sulg,
                    'email' => $user->email,
                    'mobile_no' => $user->mobile_no,
                    'roles' => [$role->name],
                    'token' => $token
                ];
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
            $token = $request->token;
            $token->revoke();
            return response()->json(['message' => 'You have been successfully logged out!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
