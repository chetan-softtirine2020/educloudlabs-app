<?php

namespace App\Jobs;

use App\Models\Topic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadCourseVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id, $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $file)
    {
        $this->id = $id;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Add google cloud code here  for file upload 
        //$path = Topic::uploadFileGoogleCloudStorage($this->file);
        info("Check Queue");
        //info($path);
        $topic = Topic::find($this->id);
        //$topic->url = $path;
        $topic->url = "Path";
        $topic->save();
    }
}
