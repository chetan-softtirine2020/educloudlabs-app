<?php

namespace App\Http\Controllers\API\LearningProvider;

use App\Http\Controllers\Controller;
use App\Models\LPTraining;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        try {
           $trainings = LPTraining::select('*')->where('user_id', Auth::user()->id)->where('status',LPTraining::ACTIVE)->latest()->take(6)->get();
           $res['list'] = $trainings;
           return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
