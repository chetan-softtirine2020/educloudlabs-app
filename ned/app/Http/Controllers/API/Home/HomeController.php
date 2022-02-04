<?php

namespace App\Http\Controllers\API\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LPTraining;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
class HomeController extends Controller
{
  
    public function allHomePageTrainings()
    {
        try {
         
            $trainings = DB::select("SELECT lp.name,lp.slug,lp.description,lp.date,u.first_name,u.last_name FROM l_p_trainings lp JOIN users u ON lp.user_id=u.id WHERE lp.status=? AND lp.is_public=? ORDER BY lp.id DESC", [1,1]);
            $res['list'] = $trainings;         
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
