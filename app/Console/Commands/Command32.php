<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command32 extends Command
{
    protected $signature = 'command32';
    protected $description = 'Command 32 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
