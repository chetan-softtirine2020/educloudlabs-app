<?php

namespace App\Http\Controllers\API\Course;

use App\Http\Controllers\Controller;
use App\Jobs\UploadCourseVideoJob;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;


class TopicsController extends Controller
{

    public function createAllCoures(Request $request)
    {
        info($request);
        $validator = Validator::make($request->all(), [
            'course_url' => 'required',
            'name' => 'required',
            'description' => 'required',
            'module_id' => 'required'
           
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $checkCourseCount = Topic::where('name', $request->name)->where('module_id', $request->module_id)->count();
            $file = $request->file('course_url');
            $topic = new Topic();
            $topic->name = $request->name;
            $topic->slug = Str::slug($request->name . " " . $checkCourseCount . " " . $request->module_id);
            $topic->description = $request->description;
            $topic->module_id = $request->module_id;
            $topic->url= Topic::uploadFileGoogleCloudStorage($file);
            $topic->save();
            // dispatch(new UploadCourseVideoJob($topic->id, $file));
            return response()->json(["message" => "Topic Added Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
