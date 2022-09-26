<?php

namespace App\Http\Controllers\API\GoogleCloud;

use App\Http\Controllers\Controller;
use App\Models\PricingChart;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CloudPriceController extends Controller
{
    public function getVMPricingChart()
    {
        try {
            $result = PricingChart::all();
            $res['list'] = $result;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function updateVMPricingChart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'memory' => 'required|numeric',
            'linux' => 'required|numeric',
            'windows' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $result = PricingChart::all();
            $res['list'] = $result;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
