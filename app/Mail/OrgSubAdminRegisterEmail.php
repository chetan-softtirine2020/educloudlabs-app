<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrgSubAdminRegisterEmail extends Mailable
{
    use Queueable, SerializesModels;
     protected $details;   
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
       $this->details=$details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Organization Admin Email')->markdown('emails.org_sub_admin_rgister_email')->with('details', $this->details);;
    }
}
