<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command40 extends Command
{
    protected $signature = 'command40';
    protected $description = 'Command 40 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
