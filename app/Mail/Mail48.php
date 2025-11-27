<?php
namespace App\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mail48 extends Mailable
{
    use SerializesModels;
    
    public function build()
    {
        return $this->view('emails.mail48');
    }
}
