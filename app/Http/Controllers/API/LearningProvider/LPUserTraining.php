<?php

namespace App\Http\Controllers\API\LearningProvider;

use App\Http\Controllers\Controller;
use App\Imports\TrainingUser;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use App\Models\LPTUser;
use Illuminate\Support\Facades\Validator;
use App\Jobs\AddLPTrainingUserJob;
use App\Models\LPTraining;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingInfo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

//use Storage;

class LPUserTraining extends Controller
{
    public function addLearningProviderTrainingUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile_no' => 'required',
            'slug' => 'required'
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $user = User::where('email', $request->email)->first();
            $password = Str::random(8);
            if (!$user) {
                $codes = User::getUserCode(Role::PROVIDER_USER, Auth::user()->id);
                $user1 = new User();
                $user1->first_name = $request->first_name;
                $user1->last_name = $request->last_name;
                $user1->email = $request->email;
                $user1->slug = User::userSlug($request->first_name, $request->last_name);
                $user1->mobile_no = $request->mobile_no;
                $user1->parent_id = Auth::user()->id;
                $user1->password = Hash::make($password);
                $user1->role = Role::PROVIDER_USER;
                $user1->name = $codes['code'];
                $user1->parent_name = $codes['parent'];
                $user1->save();
            }
            $training = LPTraining::where('slug', $request->slug)->first();
            $lptuser = new LPTUser();
            $lptuser->user_id = $user ? $user->id : $user1->id;
            $lptuser->training_id = $training->id;
            $lptuser->provider_id = Auth::user()->id;
            $lptuser->save();
            $link = "https://educloudlabs.com/training/" . $request->slug;
            ///$link="http://localhost:3000/training/" . $request->slug;            
            //Send Email  for added in training         
            $otherText = !$user ? "Use your default password for the login your account " . $password : " ";
            $description = $training['description'];
            $details['name'] = $training->name;
            $details['user_name'] = $request->first_name;
            $details['link'] = $link;
            $details['description'] = $description . " " . $otherText;
            //Mail::to($user->email)->send(new AddTrainingMail($training));
            dispatch(new AddLPTrainingUserJob($details,  $user ? $user->email : $user1->email));
            return response()->json(["message" => "Record Added Successfully."], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    ///Get all tring 
    public function getTrainingUsers(Request $request)
    {
        try {

            $trainings = DB::select('SELECT tu.min,tu.is_join,lt.slug,tu.id,u.first_name,u.last_name,lt.name,lt.link,lt.date FROM l_p_t_users tu JOIN users u ON tu.user_id=u.id JOIN l_p_trainings lt ON tu.training_id=lt.id WHERE tu.provider_id=? AND tu.status=? AND lt.slug=?', [
                Auth::user()->id, 1, $request->slug
            ]);
            $res['list'] = [];
            foreach ($trainings as $training) {
                $res['list'][] = [
                    "name" => $training->name,
                    "link" => $training->link,
                    "id" => $training->id,
                    "date" => $training->date,
                    "min" => $training->min,
                    "join" => $training->is_join == 1 ? "YES" : "NO",
                    "user_name" => $training->first_name . " " . $training->last_name
                ];
            }
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function importLearningProviderTrainingUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $file = $request->file('file');
            $training = LPTraining::where('slug', $request->slug)->first();
            $import = new TrainingUser($training->id);
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

    public function updateTrainingJoinStatus(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $training = LPTraining::where('slug', $request->slug)->first();
            LPTraining::where('slug', $request->slug)->where('user_id', Auth::user()->id)->update(['status' => 1]);
            $user = TrainingInfo::where('training_id', $training->id)->where('user_id', Auth::user()->id)->first();
            $training->status = 1; //LPTraining::START;
            $training->save();
            $trainingUser = LPTUser::where('training_id', $training->id)->where('user_id', Auth::user()->id)->first();
            $trainingUser ? $trainingUser->is_join = 1 : "";
            if (!$user) {
                $user = new TrainingInfo();
                $user->training_id = $training->id;
                $user->user_id = Auth::user()->id;
                $user->join_count = 1;
                //  $user->total_join = 1;
            } else {
                if ($user && $request->is_start) {
                    // $user->join_count = $user->join_count + 1;
                    $user->join_count = 1;
                }
                if ($user && $request->is_end) {
                    $user->join_count = $user->join_count != 0 ? $user->join_count - 1 : TrainingInfo::where('training_id', $training->id)->where('user_id', Auth::user()->id)->delete();
                    $trainingUser ? $trainingUser->min = $request->min + $trainingUser->min : "";
                }
            }
            $trainingUser ? $trainingUser->save() : "";
            $user->save();
            return response()->json(['count' => $user->join_count], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getJoinCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $training = LPTraining::where('slug', $request->slug)->first();
            //LPTraining::where('slug', $request->slug)->where('user_id', Auth::user()->id)->update(['status' => 2]);
            $user = TrainingInfo::where('training_id', $training->id)->where('user_id', Auth::user()->id)->first();
            $count = 0;
            $isAssing = 0;
            $isModerator = false;
            $chekTraning = LPTUser::where("user_id", Auth::user()->id)->where('training_id', $training->id)->first();
            if ($chekTraning) {
                $isAssing = 1;
                $isModerator = false;
            } else {
                $tr = LPTraining::where('slug', $request->slug)->where('user_id', Auth::user()->id)->first();
                if ($tr) {
                    $isAssing = 1;
                    $isModerator = true;
                }
            }
            if ($user) {
                $count = $user->join_count;
            }
            return response()->json(['count' => $count, 'isAssing' => $isAssing, 'isModerator' => $isModerator], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function reActiveUserTraining(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response($validator->getMessageBag(), 422);
        }
        try {
            $training = LPTraining::where('slug', $request->slug)->first();
            $user = TrainingInfo::where('training_id', $training->id)->where('user_id', Auth::user()->id)->first();
            if ($user && $user->join_count > 0) {
                $user->join_count = 0;
                $user->save();
            }
            return response()->json(['message' => "Reactive Training"], 202);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}



//$path = LPTraining::uploadFileGoogleCloudStorage($file);
//      $googleConfigFile = file_get_contents(storage_path('credential.json'));  
//       $user = Auth::user();
//     //create a StorageClient object
//             $storage = new StorageClient([
//                 'keyFile' => json_decode($googleConfigFile, true)
//             ]);

//     //get the bucket name from the env file
//             $storageBucketName = "educloudlab-storage";        
//     //pass in the bucket name
//             $bucket = $storage->bucket($storageBucketName);        
//             $avatar_request = $request->file('file');        
//             $image_path = $avatar_request->getRealPath();        
//     //rename the file
//             $avatar_name = $user->name.'-'.time().'.'.$avatar_request->extension();        
//     //open the file using fopen
//             $fileSource = fopen($image_path, 'r');        
//     //specify the path to the folder and sub-folder where needed
//             $googleCloudStoragePath = 'video/' . $avatar_name;               

//             //Delete previously uploaded image to cloud storage by this user
//             // if(Auth::user()->avatar !== ''){
//             //     $object = $bucket->object('laravel-upload/'.Auth::user()->avatar );
//             //     $object->delete();
//             // };        

//             //upload the new file to google cloud storage 
//             $bucket->upload($fileSource, [
//                 'predefinedAcl' => 'publicRead',
//                 'name' => $googleCloudStoragePath
//             ]);
