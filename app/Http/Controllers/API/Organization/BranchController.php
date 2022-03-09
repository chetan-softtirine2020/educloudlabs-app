<?php

namespace App\Http\Controllers\API\Organization;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function createBranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:banrches,name',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $branch = new Branch();
            $branch->name = $request->name;
            $branch->slug = Str::slug($request->name);
            $branch->save();
            return response()->json(["message" => "Record Added Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    ///Get all tring 
    public function getBarnch()
    {
        try {
            $barnches = DB::select("SELECT id,name,slug FROM branches WHERE status=?", [Branch::ACTIVE]);
            $res['list'] = $barnches;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
