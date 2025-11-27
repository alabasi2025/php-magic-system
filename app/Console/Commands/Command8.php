<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command8 extends Command
{
    protected $signature = 'command8';
    protected $description = 'Command 8 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
