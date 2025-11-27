<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command31 extends Command
{
    protected $signature = 'command31';
    protected $description = 'Command 31 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
