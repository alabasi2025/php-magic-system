<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command7 extends Command
{
    protected $signature = 'command7';
    protected $description = 'Command 7 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
