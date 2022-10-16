<?php

namespace App\Http\Controllers\API\Course;

use App\Http\Controllers\Controller;
use App\Jobs\UploadCourseVideoJob;
use App\Models\Modules;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;


class TopicsController extends Controller
{

    public function createAllCoures(Request $request)
    {

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
            $topic->url = Topic::uploadFileGoogleCloudStorage($file);
            $topic->save();
            //dispatch(new UploadCourseVideoJob($topic->id, $file));
            // UploadCourseVideoJob::dispatch($topic->id, "file");
            return response()->json(["message" => "Topic Added Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateAllCoures(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'course_url' => 'nullable|file',
            'name' => 'required',
            'description' => 'required',
            'module_id' => 'required',
            'topic_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $checkCourseCount = Topic::where('name', $request->name)->where('module_id', $request->module_id)->count();
            $file = $request->file('course_url');
            $topic = Topic::find($request->topic_id);
            $topic->name = $request->name;
            $topic->slug = Str::slug($request->name . " " . $checkCourseCount . " " . $request->module_id);
            $topic->description = $request->description;
            $topic->module_id = $request->module_id;
            if ($file) {
                $topic->url = Topic::uploadFileGoogleCloudStorage($file);
            }
            $topic->save();
            //dispatch(new UploadCourseVideoJob($topic->id, $file));
            // UploadCourseVideoJob::dispatch($topic->id, "file");
            return response()->json(["message" => "Topic Update Successfully."], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function getCureseModuleTopicWiseList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $data = DB::select(
                'SELECT c.name as course,m.name as module,t.name as topic,t.id as tid,t.slug as tslug FROM topics as t 
                 JOIN modules as m ON m.id=t.module_id
                 JOIN courses as c ON m.course_id=c.id WHERE c.slug=?',
                [$request->slug]
            );
            $res['list'] = $data;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function getTopicEditData(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {

            $topic = Topic::select('*')->where('slug', $request->slug)->first();
            $module = Modules::find($topic->module_id);
            $modules = Modules::select('name', 'slug', 'id')->where('course_id', $module->course_id)->where('status', Modules::ACTIVE)->get();
            $res = [
                'id' => $topic->id,
                'slug' => $topic->slug,
                'name' => $topic->name,
                'description' => $topic->description,
                'url' => $topic->url,
                'module_id' => $topic->module_id,
                'course_id' => $module->course_id,
                'modules' => $modules,
            ];
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function deleteTopic(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            Topic::where('slug', $request->slug)->delete();
            return response()->json(['message' => 'Topic Deleted Sucessfully'], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
