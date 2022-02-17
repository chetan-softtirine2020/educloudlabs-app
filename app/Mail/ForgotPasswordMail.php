<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject = "Your password reset request";

    private $name, $url;
     
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->subject($this->subject)->markdown('emails.forgetPassword', ['name' => $this->name, 'url' => $this->url]);
    }
}
