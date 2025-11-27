<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command23 extends Command
{
    protected $signature = 'command23';
    protected $description = 'Command 23 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
