<?php

namespace App\Http\Controllers\API\LearningProvider;

use App\Http\Controllers\Controller;
use App\Models\LPTraining;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        try {
            info("Auth Id");
            info(Auth::user()->id);
            $trainings = DB::select("SELECT lp.name,lp.slug,lp.description,lp.date,lp.id FROM l_p_trainings lp WHERE   lp.user_id AND lp.status=?  ORDER BY lp.id DESC LIMIT 3", [Auth::user()->id, LPTraining::ACTIVE]);
            $res['list'] = $trainings;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
