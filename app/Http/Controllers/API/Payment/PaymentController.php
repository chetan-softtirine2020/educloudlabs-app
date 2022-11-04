<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Models\GCUser;
use App\Models\PaymentHirstory;
use App\Models\User;
use App\Models\VMDetails;
use App\Models\VmPaymentDetails;
use App\Models\VMUsed;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function getPaymentDetails(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://api.razorpay.com/v1/payments/pay_KSQ16GlFdRSHIt');
        $response = $response->getBody()->getContents();
        return response()->json($response, 200);
    }




    public function getVMBillingDetails(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string'
        // ]);
        // if ($validator->fails()) {
        //     return response($validator->getMessageBag(), 422);
        // }
        try {
             $gcUser=GCUser::where('user_id',Auth::user()->id)->orderBy('id','DESC')->first();
           // $result1=VMDetails::select('vm_name','storage','ram','total_cost','created')->where('user_id',$gcUser->id)->where('status',!= VMDetails::DELETE)->get();  
            $result = DB::select("SELECT vd.vm_name,u.first_name,u.last_name,vu.vm_start,vu.vm_stop,vu.used_min,vu.cost,vu.id FROM v_m_useds vu JOIN users u ON vu.assign_user_id=u.id JOIN v_m_details as vd ON vd.id=vu.vm_id WHERE vu.is_paid=? AND vu.assign_by=?", [0, Auth::user()->id]);
            $totalCost = DB::select("SELECT SUM(vu.cost) as cost FROM v_m_useds vu  WHERE vu.is_paid=? AND assign_by=?", [0, Auth::user()->id]);
            $user = User::select('email', 'mobile_no', 'first_name', 'last_name')->where('id', Auth::user()->id)->first();
            // $client = new \GuzzleHttp\Client();
            // $response = $client->get('https://api.razorpay.com/v1/payments/pay_KSQ16GlFdRSHIt');
            // $response = $response->getBody()->getContents();
            //please provide your api key for authentication purposes razorpay
            //base64_encode();
            $res = [
                'details' => $result,
                'totalCost' => round($totalCost[0]->cost, 2),
                'user' => $user,
                //   'res' => $response
            ];
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function saveVmPaymenetDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $totalCost = DB::select("SELECT SUM(vu.cost) as cost FROM v_m_useds vu  WHERE vu.is_paid=? AND assign_by=?", [0, Auth::user()->id]);
            $paymentHistory = new PaymentHirstory();
            $paymentHistory->payment_id = $request->payment_id;
            $paymentHistory->user_id = Auth::user()->id;
            $paymentHistory->amount =  round($totalCost[0]->cost, 2);
            $paymentHistory->payment_for = 1;
            $paymentHistory->save();

            $results = DB::select("SELECT vd.vm_name,u.first_name,u.last_name,vu.vm_start,vu.vm_stop,vu.used_min,vu.cost,vu.id FROM v_m_useds vu JOIN users u ON vu.assign_user_id=u.id JOIN v_m_details as vd ON vd.id=vu.vm_id WHERE vu.is_paid=? AND vu.assign_by=?", [0, Auth::user()->id]);
            foreach ($results as $detail) {
                $vmpaymentdetails = new VmPaymentDetails();
                $vmpaymentdetails->vmused_id = $detail->id;
                $vmpaymentdetails->payment_id = $paymentHistory->id;
                $vmpaymentdetails->amount = round($detail->cost, 2);
                $vmpaymentdetails->save();
                VMUsed::where('is_paid', 0)->where('assign_by', Auth::user()->id)->update(['is_paid' => 1]);
            }
            return response()->json(['message' => "Payment Updated Successfully"], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function getLPVmPaymentHistory()
    {
        try {
            $data = PaymentHirstory::where('user_id', Auth::user()->id)->where('payment_for', 1)->get();
            $res['list'] = $data;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function getAdminVmPaymentHistory()
    {
        try {
            $data = PaymentHirstory::where('payment_for', 1)->get();
            $res['list'] = $data;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

        


}
