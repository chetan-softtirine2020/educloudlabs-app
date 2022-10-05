<?php

namespace App\Http\Controllers\API\LearningProvider\ProviderUser;

use App\Http\Controllers\Controller;
use App\Models\LPTUser;
use App\Models\VMUsed;
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
            $trainings = DB::select("SELECT tu.is_join,tu.id,lt.slug,lt.name,lt.link,lt.date FROM l_p_t_users tu JOIN users u ON tu.user_id=u.id JOIN l_p_trainings lt ON tu.training_id=lt.id WHERE tu.user_id=? AND tu.status=?", [
                Auth::user()->id, LPTUser::ACTIVE
            ]);
            $res['list'] = [];
            foreach ($trainings as $training) {
                $res['list'][] = [
                    "name" => $training->name,
                    "link" => $training->link,
                    "slug" => $training->slug,
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
        try {
            $training = LPTUser::find($request->id);
            $training->is_join = 1;
            $training->save();
            return response()->json(["message" => "Record updated successfully "], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getLPUDashboarData(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $vmData = VMUsed::select('v_m_details.vm_name', 'v_m_useds.created_at')->where('assign_user_id', $user_id)
                ->join('v_m_details', 'v_m_useds.vm_id', '=', 'v_m_details.id')
                ->where('v_m_useds.status', 1)
                ->orderBy('v_m_useds.id', 'DESC')->first();

               
            $res = [];
            $res['vm_data'] = $vmData;
            $res['training_data'] = $vmData;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
