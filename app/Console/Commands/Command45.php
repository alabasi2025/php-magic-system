<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command45 extends Command
{
    protected $signature = 'command45';
    protected $description = 'Command 45 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
