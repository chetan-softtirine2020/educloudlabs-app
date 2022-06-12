<?php

namespace App\Http\Controllers\API\Course;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class ModulesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createCoureseModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'course_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {

            $module = new Modules();
            $module->name = $request->name;
            $module->slug = Str::slug($request->name . "_" . $request->course_id);
            $module->course_id = $request->course_id;
            $module->save();
            return response()->json(["message" => "Module Added Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function getCoureseModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $course = Modules::where('slug', $request->slug)->first();
            return response()->json($course, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function updateCourseModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'course_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $module = Modules::find($request->id);
            $module->name = $request->name;
            $module->slug = Str::slug($request->name . "_" . $request->course_id);
            $module->course_id = $request->course_id;
            $module->save();
            return response()->json(["message" => "Module Update Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function getAllCoureseModules(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $modules = Modules::select('name', 'slug', 'id')->where('course_id', $request->course_id)->where('status', Modules::ACTIVE)->get();
            $res['list']=$modules;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
