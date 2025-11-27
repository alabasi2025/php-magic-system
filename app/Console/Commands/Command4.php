<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command4 extends Command
{
    protected $signature = 'command4';
    protected $description = 'Command 4 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
