<?php

namespace App\Http\Controllers\API\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Modules;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createCourese(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'amount' => 'nullable|numeric'
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {           
            $checkCourseCount = Course::where('name', $request->name)->where('user_id', Auth::user()->id)->count();
            $course = new Course();
            $course->name = $request->name;
            $course->slug = Str::slug($request->name . " " . $checkCourseCount . 1 . " " . Auth::user()->slug);
            $course->description = $request->description;
            $course->amount = $request->amount;
            $course->user_id = Auth::user()->id;
            $course->is_paid = 0;
            $course->save();
           return response()->json(["message" => "Record Added Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function getCourese(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $course = Course::where('slug', $request->slug)->first();
            return response()->json($course, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function updateCourese(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'amount' => ['nullable', 'numeric']
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $checkCourseCount = Course::where('name', $request->name)->count();
            $course = Course::find($request->id);
            $course->name = $request->name;
            // $course->slug = Str::slug($request->name . "_" . $checkCourseCount + 1);
            $course->description = $request->description;
            $course->amount = $request->amount;
            $course->user_id = Auth::user()->id;
            $course->is_paid = 0;
            $course->save();
            return response()->json(["message" => "Course Update Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function getAllCoureses(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'slug' => 'required',
        // ]);
        // if ($validator->fails()) {
        //     return response($validator->getMessageBag(), 422);
        // }
        try {
            $course = Course::select('name', 'slug', 'id', 'description', 'amount')->where('user_id', Auth::user()->id)->where('status', Course::ACTIVE)->get();
            $res['list'] = $course;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function getCoursesForPlay(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $course = Course::select('name','id','description','slug')->where('slug', $request->slug)->where('user_id', Auth::user()->id)->first();
            $data = Modules::select('id', 'name', 'slug')->where('course_id', $course->id)->get();
            
            foreach ($data as $d) {
                $topic = Topic::where('module_id', $d->id)->get();                
                $d['topic'] = $topic;                           
               }      
              $courseData['data']=$data;
              $courseData['course']=$course;   
            return response()->json($courseData, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
   

    public function deleteCourese(Request $request)
    {
     //$result= DB::select("SELECT p.product_name, s.quntiy,s.date FROM stocks AS s JOIN product AS p ON s.product_id=p.id");     
         
    }
}
