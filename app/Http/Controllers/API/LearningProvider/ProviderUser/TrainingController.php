<?php

namespace App\Http\Controllers\API\LearningProvider\ProviderUser;

use App\Http\Controllers\Controller;
use App\Models\LPTUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class TrainingController extends Controller
{
    public function getTrainingUsersWise()
    {
        try {
            $trainings = DB::select("SELECT tu.is_join,tu.id,lt.name,lt.link,lt.date FROM l_p_t_users tu JOIN users u ON tu.user_id=u.id JOIN l_p_trainings lt ON tu.training_id=lt.id WHERE tu.user_id=? AND tu.status=?", [
                Auth::user()->id, 1
            ]);            
            $res['list'] = [];
            foreach ($trainings as $training) {
                $res['list'][] = [
                    "name" => $training->name,
                    "link" => $training->link,
                    "id" => $training->id,
                    "date" => $training->date,
                    "join" => $training->is_join == 1 ? "YES" : "NO",                
                ];
            }
            return response()->json($res, 200);
        } catch (Exception $e) {           
            return response()->json(['message' => $e->getMessage()]);
        }
    }

     public function updateTrainingJoinStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',          
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try{
             $training=LPTUser::find($request->id);
             $training->is_join=1;  
             $training->save();   
             return response()->json(["message" => "Record updated successfully "], 202);        
        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }

}
