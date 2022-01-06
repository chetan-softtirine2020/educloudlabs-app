<?php

namespace App\Http\Controllers\API\Organization;

use App\Http\Controllers\Controller;
use App\Models\OrgTraining;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createOrgTraining(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:l_p_trainings,name',
            'date' => 'required|date',
            'description' => 'required',
            'link' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $training = new OrgTraining();
            $training->name = $request->name;
            $training->slug = Str::slug($request->name);
            $training->date = date('Y-m-d h:i:s', strtotime($request->date));
            $training->link = $request->link;
            $training->description = $request->description;
            $training->user_id = Auth::user()->id;
            $training->save();
           return response()->json(["message" => "Record Added Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
  
    public function allOrgTrainings()
    {
        try {
            $trainings = DB::select("SELECT * FROM org_trainings WHERE status=? AND user_id=?", [1, Auth::user()->id]);
            $res['list'] = [];
            foreach ($trainings as $training) {
                $res['list'][] = [
                    "name" => $training->name,
                    "link" => $training->link,
                    "slug" => $training->slug,
                    "id" => $training->id,
                    "date" => $training->date,
                    "description" => $training->description,
                ];
            }
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }   

}
