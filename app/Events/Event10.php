<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Event10
{
    use Dispatchable, SerializesModels;
    
    public function __construct()
    {
        //
    }
}
