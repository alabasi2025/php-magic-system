<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command44 extends Command
{
    protected $signature = 'command44';
    protected $description = 'Command 44 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
