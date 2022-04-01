<?php

namespace App\Http\Controllers\API\Organization;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    
    public function createDepartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:departments,name',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $department = new Department();
            $department->name = $request->name;
            $department->slug = Str::slug($request->name);
            $department->save(); 
            return response()->json(["message" => "Record Added Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    ///Get all Department 
    public function getDepartments()
    {
        try {
            $departments = DB::select("SELECT id,name,slug FROM departments WHERE status=?", [Department::ACTIVE]);
            $res['list'] = $departments;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

}



