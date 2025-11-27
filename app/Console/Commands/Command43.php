<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command43 extends Command
{
    protected $signature = 'command43';
    protected $description = 'Command 43 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
