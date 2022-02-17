<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\ForgotPasswordJob;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $user = User::where('email', $request->email)->first();
            $token = Str::random(35);
            if ($user) {
                DB::table('password_resets')->where('email', $user->email)->delete();
            }
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            // $hid = Crypt::encrypt($user->id);
            $link = "https://educloudlabs.com" . "/reset-password/$token";
            // $link = "http://localhost:3000" . "/reset-password/$token";
            Mail::to($request->email)->send(new ForgotPasswordMail($user->first_name, $link));
            // dispatch(new ForgotPasswordJob($user->first_name, $user->email, $link));
            return response()->json(["message" => "We have e-mailed your password reset link!"], 201);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'i_t' => 'required|string',
            'password' => 'required|confirmed',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $user = PasswordReset::where('token', $request->i_t)->first();
            $u = User::where('email', $user->email)->first();
            if ($user) {
                $result = PasswordReset::where('token', $request->i_t)->first();
                if ($result != "") {
                    PasswordReset::where('email', $u->email)->where('token', $request->i_t)->delete();
                    User::where('id', $u->id)->update(['password' => bcrypt($request->password)]);
                    return response()->json(["message" => "Password reset successfully!"], 202);
                }
            } else {
                return response()->json(["message" => "Token is invalid"], 403);
            }
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }
}
