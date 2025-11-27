<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command41 extends Command
{
    protected $signature = 'command41';
    protected $description = 'Command 41 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
