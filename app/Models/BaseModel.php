<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Storage\StorageClient;

use Illuminate\Support\Facades\Auth;

class BaseModel extends Model
{
    const ACTIVE = 1;
    const INACTIVE = 0;
    const APPROVED = 1;
    const APPROVE = 0;
   //Traning 
    const REGISTER=1;
    const START=2;
    const COMPLETED=3;

   public static function uploadFileGoogleCloudStorage($file){
             $googleConfigFile = file_get_contents(storage_path('credential.json'));  
              $user = Auth::user();
            //create a StorageClient object
                    $storage = new StorageClient([
                        'keyFile' => json_decode($googleConfigFile, true)
                    ]);
            //get the bucket name from the env file
                    $storageBucketName = "educloudlab-storage";        
            //pass in the bucket name
                    $bucket = $storage->bucket($storageBucketName);        
                    //$avatar_request = $request->file('file');        
                    $avatar_request = $file; 
                    $image_path = $avatar_request->getRealPath();        
            //rename the file
                    $avatar_name = $user->name.'-'.time().'.'.$avatar_request->extension();        
            //open the file using fopen
                    $fileSource = fopen($image_path, 'r');        
            //specify the path to the folder and sub-folder where needed
                    $googleCloudStoragePath = 'video/' . $avatar_name;               

                    //Delete previously uploaded image to cloud storage by this user
                    // if(Auth::user()->avatar !== ''){
                    //     $object = $bucket->object('laravel-upload/'.Auth::user()->avatar );
                    //     $object->delete();
                    // };       
                    //upload the new file to google cloud storage 
                    $bucket->upload($fileSource, [
                        'predefinedAcl' => 'publicRead',
                        'name' => $googleCloudStoragePath
                    ]);
            return $path="https://storage.googleapis.com/educloudlab-storage/".$googleCloudStoragePath;
           
        }
}
