<?php

namespace App\Http\Controllers\API\GoogleCloud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\VMTrainingInvitationImport;
use App\Models\GCUser;
use App\Models\VMDetails;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CGPVMController extends Controller
{

    public function getVmCount()
    {
        try {
            $vmUserId = GCUser::where("user_id", Auth::user()->id)->orderBy('id', 'DESC')->first();
            $count = VMDetails::where('user_id', $vmUserId->id)->where('is_assign', 0)->count();
            return response($count, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function importVMTrainingUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $file = $request->file('file');
            $import = new VMTrainingInvitationImport();
            $import->import($file);
            if ($import->errors()->isNotEmpty()) {
                return response()->json(['message' => "Something went worng"], 500);
            }
            if ($import->failures()->isNotEmpty()) {
                return response($import->failures(), 422);
                //info($import->failures());
            }
            return response()->json(['message' => "Record added suceessfully"], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function getAssignVMDetails(Request $request)
    {
         $validator = Validator::make($request->all(), [
              'name' => 'required|string'
         ]);
         if ($validator->fails()) {
              return response($validator->getMessageBag(), 422);
         }
         try {
              $result = DB::select("SELECT u.first_name,u.last_name,u.id as u_id,vu.vm_start,vu.vm_stop,vu.used_min,vu.status,vu.cost FROM v_m_useds vu JOIN users u ON vu.assign_user_id=u.id JOIN v_m_details as vd ON vd.id=vu.vm_id WHERE vd.vm_name=?", [$request->name]);
              $res['list'] = $result;
              return response()->json($res, 200);
         } catch (Exception $e) {
              return response()->json(['message' => $e->getMessage()]);
         }
    }

  


  
}
