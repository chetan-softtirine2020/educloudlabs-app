<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public  function uploadFile()
    {

        return view('fileupload');
    }


    public  function uploadAddFile(Request $request)
    {
        
            $file = $request->file('image');
           //  dd($request);
            $disk = Storage::disk('gcs');
            $disk->put('hola.txt', 'conterr data');
           // return redirect('file');
        
    }
}
