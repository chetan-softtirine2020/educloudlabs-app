<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class UserController extends Controller
{
    public function getUserList(Request $request)
    {
        try {
            $users = DB::select('SELECT first_name,last_name,email,mobile_no,id,slug FROM users WHERE role=?', [$request->role]);
            $res['list'] = $users;
            return response()->json($res, 200);
        } catch (Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function getUserChildUser(Request $request)
    {
        try {
            $users = DB::select('SELECT first_name,last_name,email,mobile_no,id,slug FROM users WHERE parent_id=?', [$request->id]);
            $res['list'] = $users;
            return response()->json($res, 200);
        } catch (Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    
}
