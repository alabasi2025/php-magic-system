<?php
namespace App\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mail37 extends Mailable
{
    use SerializesModels;
    
    public function build()
    {
        return $this->view('emails.mail37');
    }
}
