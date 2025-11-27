<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command20 extends Command
{
    protected $signature = 'command20';
    protected $description = 'Command 20 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
