<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command1 extends Command
{
    protected $signature = 'command1';
    protected $description = 'Command 1 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
