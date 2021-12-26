<?php

namespace App\Http\Controllers\API\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Organization;
use Exception;

class OrganizationController extends Controller
{
    public function createOrganization(Request $request)
    {
         info($request);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',          
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $org = new Organization();
            $org->name = $request->name;
            $org->email = $request->email;
            $org->description = $request->description;
            $org->status = 0;
            $org->is_approved = 0;
            $org->save();

            return response()->json(["message" => "Record Added Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getOrganizationForApproved()
    {
        try {
            $orgs = DB::select('SELECT * FROM organizations where status = ? AND is_approved=?', [0, 0]);
            $res['list'] = [];
            foreach ($orgs as $org) {
                $res['list'][] = [
                    "name" => $org->name,
                    "email" => $org->email,
                    "id" => $org->id,
                    "is_approved" => $org->is_approved,
                ];
            }
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getOrganizationsForRegister()
    {
        try {
            $resuls = DB::select('SELECT * FROM organizations where status = ? AND is_approved=?', [1, 1]);
            return response()->json(["result" => $resuls], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function approvedOrganization(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'org_id' => 'required',
            'is_approved' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $org = Organization::find($request->org_id);
            if ($request->is_approved == 1) {
                $org->status = 1;
            }
            $org->is_approved = $request->is_approved;
            $org->save();
            if ($request->is_approved == 1) {
                //Send Approved Email Data 
            } else {
                //Send Not Approved Email Data 
            }

            return response()->json(["message" => "Record approved Successfully."], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
