<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\OrgUserApprovedEmail;
use Illuminate\Support\Facades\Mail;

class SendOrgUserRequestStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details,$email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details,$email)
    {
         $this->details=$details;
         $this->email=$email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new OrgUserApprovedEmail($this->details));
    }
}
