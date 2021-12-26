<?php

namespace App\Http\Controllers\API\LearningProvider;

use Exception;
use App\Models\LPTraining;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createTraining(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'date' => 'required|date',
            'description' => 'nullable',
            'link' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $training = new LPTraining();
            $training->name = $request->name;
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
    public function getTraining(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        try {
            $training = LPTraining::find($request->id);
            $res['success'] = [
                "name" => $training->name,
                "link" => $training->link,
                "id" => $training->id,
                "description" => $training->description,
            ];

            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function updateTraining(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'date' => 'required|date',
            'description' => 'nullable',
            'link' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        try {
            $training = LPTraining::find($request->id);
            $training->name = $request->name;
            $training->date = $request->date;
            $training->link = $request->link;
            $training->description = $request->description;
            $training->user_id = 3;
            $training->save();

            return response()->json(["message" => "Record Updated Successfully."], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function allTrainings()
    {
        try {
            $trainings = DB::select('SELECT * FROM l_p_trainings WHERE status=1');
            foreach ($trainings as $training) {
                $res['list'][] = [
                    "name" => $training->name,
                    "link" => $training->link,
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
