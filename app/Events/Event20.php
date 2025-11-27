<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Event20
{
    use Dispatchable, SerializesModels;
    
    public function __construct()
    {
        //
    }
}
