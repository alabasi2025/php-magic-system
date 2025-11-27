<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command25 extends Command
{
    protected $signature = 'command25';
    protected $description = 'Command 25 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
