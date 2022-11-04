<?php

namespace App\Http\Controllers\API\GoogleCloud;

use App\Http\Controllers\Controller;
use App\Models\BaseModel;
use App\Models\GCUser;
use App\Models\PricingChart;
use App\Models\VMDetails;
use App\Models\VMUsed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GCPUserController extends Controller
{

     public function registerGCPUser(Request $request)
     {
          $validator = Validator::make($request->all(), [
               'username' => 'required|string|unique:g_c_users,username|min:1',
               'email' => 'required|email|unique:g_c_users,email|min:1',
               'password' => 'required|min:8',
               'password2' => 'required|min:8',
               'first_name' => 'required|string',
               'last_name' => 'required|string',
          ]);
          if ($validator->fails()) {
               return response($validator->getMessageBag(), 422);
          }
          try {
               //Register user 
               $client = new \GuzzleHttp\Client();
               $response = $client->request('POST', BaseModel::VMURL . '/accounts/signup', [
                    'form_params' => [
                         'username' => $request->username,
                         'password' => $request->password,
                         'password2' => $request->password2,
                         'email' => $request->email,
                         'first_name' => $request->first_name,
                         'last_name' => $request->last_name
                    ]
               ]);
               $response = $response->getBody()->getContents();
               if ($response) {
                    $gcuser = new GCUser();
                    $gcuser->username = $request->username;
                    $gcuser->email = $request->email;
                    $gcuser->first_name = $request->first_name;
                    $gcuser->last_name = $request->last_name;
                    $gcuser->password = $request->password;
                    $gcuser->password2 = $request->password2;
                    $gcuser->user_id = Auth::user()->id;
                    $gcuser->save();
               }
               return response()->json(["message" => "User Resiter Successfully."], 201);
          } catch (Exception $e) {
               return response()->json(['message' => $e->getMessage()], 500);
          }
     }


     public function authorizeGoogleAccount(Request $request)
     {
          $validator = Validator::make($request->all(), [
               'username' => 'required|string|exists:g_c_users,username|min:1',
               'password' => 'required',
          ]);
          if ($validator->fails()) {
               return response($validator->getMessageBag(), 422);
          }
          try {

               $checkGcuser = GCUser::where('username', $request->username)->where('password', $request->password)->first();
               info($checkGcuser);
               if (!$checkGcuser) {
                    return response()->json(['message' => "No active account found with the given credentials"], 403);
               }

               $client = new \GuzzleHttp\Client();
               $response = $client->request('POST', BaseModel::VMURL . '/accounts/api/token/', [
                    'form_params' => [
                         'username' => $request->username,
                         'password' => $request->password
                    ]
               ]);
               $response = $response->getBody()->getContents();
               $res = json_decode($response, true);
               $gcuser = GCUser::where('username', $request->username)->first();
               $gcuser->token = $res['access'];
               $gcuser->refreshToken = $res['refresh'];
               $gcuser->save();
               return response()->json(["message" => $res['access']], 201);
          } catch (Exception $e) {
               info(json_decode($e->getMessage()));
               return response()->json(['message' => $e->getMessage()], 500);
          }
     }


     public function refreshApiToken(Request $request)
     {
          $validator = Validator::make($request->all(), [
               'username' => 'required|string|min:1',
               'password' => 'required',
          ]);
          if ($validator->fails()) {
               return response($validator->getMessageBag(), 422);
          }
          try {
          } catch (Exception $e) {
               return response()->json(['message' => $e->getMessage()], 500);
          }
     }

     public function createVm(Request $request)
     {
          $validator = Validator::make($request->all(), [
               'image' => 'required|string|min:1',
               'protocol' => 'required|string',
               'zone' => 'required',
               'count' => 'numeric|min:1',
               'softwares' => 'required',
               'storage' => 'numeric|min:10|max:100',
               'ram' => 'required',
          ]);
          if ($validator->fails()) {
               return response($validator->getMessageBag(), 422);
          }
          try {
               $token = $this->authorizeGcaccount();
               $getToken = GCUser::where("user_id", Auth::user()->id)->orderBy('id', 'DESC')->first();
               if ($token) {
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('POST', 'http://35.200.161.241/vm/', [
                         'headers' => ['Authorization' => 'Bearer ' . $token],
                         'form_params' => [
                              'image' => $request->image,
                              'protocol' => $request->protocol,
                              'zone' => $request->zone,
                              'count' => $request->count,
                              'softwares' => $request->softwares,
                              'storage' => $request->storage,
                              'ram' => $request->ram
                         ]
                    ]);
                    $response = $response->getBody()->getContents();
                    $res = json_decode($response, true);

                    if ($res) {
                         for ($i = 0; $i < $request->count; $i++) {
                              $vmDetails = new VMDetails();
                              $vmDetails->user_id = $getToken->id;
                              $vmDetails->image = $request->image;
                              $vmDetails->protocol = $request->protocol;
                              $vmDetails->zone = $request->zone;
                              $vmDetails->storage = $request->storage;
                              $vmDetails->ram = $request->ram;
                              $vmDetails->softwares = $request->softwares;
                              $vmDetails->vm_name = $res[$i];
                              $vmDetails->save();
                         }
                    }

                    return response()->json(["message" => "VM Created Successfully"], 201);
               }
          } catch (Exception $e) {
               return response()->json(['messagee' => $e->getMessage()], 500);
          }
     }


     public function getVmList()
     {
          try {
               $vmUser = GCUser::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->first();
               $vms = [];
               if ($vmUser) {
                    $vms = DB::select("SELECT vm_name,status,image,ram,storage,id,protocol,created,zone FROM v_m_details WHERE user_id=?", [$vmUser->id]);
               }
               $res['list'] = $vms;
               return response()->json($res, 200);
          } catch (Exception $e) {
               return response()->json(['message' => $e->getMessage()]);
          }
     }

     public function checkUserGCAccoutExist()
     {
          try {
               $vmUser = GCUser::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->first();
               $is_created = '';
               if ($vmUser) {
                    $is_created = true;
               } else {
                    $is_created = false;
               }
               return response()->json(['is_created' => $is_created], 200);
          } catch (Exception $e) {
               return response()->json(['message' => $e->getMessage()]);
          }
     }

     public function vmStartAndStop(Request $request)
     {
          $validator = Validator::make($request->all(), [
               'action' => 'required|string|min:1',
               'name' => 'required|string'
          ]);
          if ($validator->fails()) {
               return response($validator->getMessageBag(), 422);
          }
          try {
               DB::beginTransaction();
               $token = $this->authorizeGcaccount();
               if ($token) {
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('PATCH',  'http://35.200.161.241/vm/', [
                         'headers' => ['Authorization' => 'Bearer ' . $token],
                         'form_params' => [
                              'action' => $request->action,
                              'name' => $request->name,
                         ]
                    ]);
                    $response = $response->getBody()->getContents();
                    $updateStopStart = VMDetails::where('vm_name', $request->name)->first();
                    $updateStopStart->status = $request->action == 'start' ? VMDetails::START : VMDetails::STOP;
                    $updateStopStart->save();

                    $vmUsed = VMUsed::where('vm_id', $updateStopStart->id)->where('assign_user_id', Auth::user()->id)->orderBy('id', 'DESC')->first();
                    if ($request->action == 'stop' && $vmUsed) {
                         $date = \Carbon\Carbon::now();
                         $currentDate = $date->format('Y-m-d H:i:s');
                         $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i',  $currentDate);
                         $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $vmUsed->vm_start);
                         $total_minutes = $to->diffInMinutes($from);
                         $vmUsed->used_min = $total_minutes / 60;
                         $vmUsed->vm_stop = \Carbon\Carbon::now();
                         $vmUsed->cost = $this->getTotalCost($total_minutes / 60, $updateStopStart);
                         $vmUsed->save();
                    } else if ($request->action == 'start' && $request->is_user) {
                         if ($vmUsed && $vmUsed->vm_start == NULL) {
                              $vmUsed->vm_start = \Carbon\Carbon::now();
                              $vmUsed->save();
                         } else {
                              $gcuser = GCUser::where('id', $updateStopStart->user_id)->first();
                              $usedVm = new VMUsed();
                              $usedVm->vm_id = $updateStopStart->id;
                              $usedVm->assign_by = $gcuser->user_id;
                              $usedVm->assign_user_id = Auth::user()->id;
                              $usedVm->vm_start = \Carbon\Carbon::now();
                              $usedVm->save();
                         }
                    }
               }
               DB::commit();
               return response()->json(["message" => "VM updated sucessfully"], 202);
          } catch (Exception $e) {
               DB::rollBack();
               return response()->json(['message' => $e->getMessage()]);
          }
     }

     public function startStopMultipleVM(Request $request)
     {
          $validator = Validator::make($request->all(), [
               'action' => 'required|string|min:1',
               'name' => 'required|string'
          ]);
          if ($validator->fails()) {
               return response($validator->getMessageBag(), 422);
          }

          try {
               $token = $this->authorizeGcaccount();
               if ($token) {
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('PATCH',  'http://35.200.161.241/vm/', [
                         'headers' => ['Authorization' => 'Bearer ' . $token],
                         'form_params' => [
                              'action' => $request->action,
                              'name' => $request->name
                         ]
                    ]);
                    $response = $response->getBody()->getContents();
                    $updateStopStart = VMDetails::where('vm_name', $request->name)->first();
                    $updateStopStart->status = $request->action == 'start' ? VMDetails::START : VMDetails::STOP;
                    $updateStopStart->save();
               }
          } catch (Exception $e) {
               return response()->json(['message' => $e->getMessage()]);
          }
     }

     public function vmDelete(Request $request)
     {
          $validator = Validator::make($request->all(), [
               'name' => 'required|string'
          ]);
          if ($validator->fails()) {
               return response($validator->getMessageBag(), 422);
          }
          try {
               $token = $this->authorizeGcaccount();
               $getToken = GCUser::where("user_id", Auth::user()->id)->first();
               if ($token) {
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('DELETE', 'http://35.200.161.241/vm/', [
                         'headers' => ['Authorization' => 'Bearer ' . $token],
                         'form_params' => [
                              'name' => $request->name,
                         ]
                    ]);
                    $response = $response->getBody()->getContents();
                    $vm = VMDetails::where('vm_name', $request->name)->first();
                    $storage = PricingChart::where('storage_price', !NULL)->first();
                    $vm->status = VMDetails::DELETE;
                    $date = \Carbon\Carbon::now();
                    $currentDate = $date->format('Y-m-d H:i:s');
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i',  $currentDate);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $vm->created);
                    $total_minutes = $to->diffInMinutes($from);
                    $days = $from->diffInDays($to);
                    $vm->deleted = $currentDate;
                    $vm->used_min = $total_minutes / 60;
                    $vm->vm_cost = $this->getTotalCost($total_minutes / 60, $vm);
                    $storageCost = round($storage->storage_price * $vm->storage, 2);
                    $month  = $this->getTotalMonth($days);
                    $totalStorageCost = $storageCost * $month;
                    $vm->storage_cost = $totalStorageCost;
                    $vm->total_cost = $totalStorageCost + $vm->vm_cost;
                    $vm->save();
               }
               return response()->json(["message" => "VM deleted sucessfully"], 202);
          } catch (Exception $e) {
               return response()->json(['message' => $e->getMessage()]);
          }
     }

     public function addVmToUser(Request $request)
     {
          $validator = Validator::make($request->all(), [
               'name' => 'required|string'
          ]);
          if ($validator->fails()) {
               return response($validator->getMessageBag(), 422);
          }
          try {
          } catch (Exception $e) {
               return response()->json(['message' => $e->getMessage()]);
          }
     }


     public function authorizeGcaccount()
     {

          $user = GCUser::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
          if (!$user) {
               $usedVm = VMUsed::where('assign_user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
               $user_id = $usedVm->assign_by;
               $user = GCUser::where('user_id', $user_id)->orderBy('id', 'desc')->first();
          }
          $client = new \GuzzleHttp\Client();
          $response = $client->request('POST', 'http://35.200.161.241/accounts/api/token/', [
               'form_params' => [
                    'username' => $user->username,
                    'password' => $user->password
               ]
          ]);
          $response = $response->getBody()->getContents();
          $res = json_decode($response, true);
          return $res['access'];
          // info($user);
          //GCUser::where('id', $user->id)->update(['token' => $res['access'], 'refreshToken' => $res['refresh']]);
     }


     public function getTotalCost($hr, $vm)
     {
          $getCost = PricingChart::where('memory', $vm->ram)->first();
          if ($vm->image == "windows") {
               $price = $getCost->windows;
          } else {
               $price = $getCost->linux;
          }
          return   $hr * $price;
     }

     public function getTotalMonth($days)
     {
          if ($days >= 1 && $days <= 30) {
               $month = 1;
          } else if ($days >= 31 && $days <= 60) {
               $month = 2;
          } else if ($days >= 61 && $days <= 90) {
               $month = 3;
          } else if ($days >= 91 && $days <= 120) {
               $month = 4;
          } else if ($days >= 121 && $days <= 150) {
               $month = 5;
          } else if ($days >= 151 && $days <= 180) {
               $month = 6;
          } else if ($days >= 181 && $days <= 210) {
               $month = 7;
          } else if ($days >= 211 && $days <= 240) {
               $month = 8;
          } else if ($days >= 241 && $days <= 270) {
               $month = 9;
          } else if ($days >= 271 && $days <= 300) {
               $month = 10;
          } else {
               $month = 0;
          }
          return $month;
     }
}
