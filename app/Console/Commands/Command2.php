<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command2 extends Command
{
    protected $signature = 'command2';
    protected $description = 'Command 2 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
