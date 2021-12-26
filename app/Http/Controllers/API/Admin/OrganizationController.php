<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    public function getAllOrganizations(Request $request)
    {
        try {
            $orgs = DB::select('SELECT * FROM organizations WHERE is_approved=?', [0]);
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
            info($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
