<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command30 extends Command
{
    protected $signature = 'command30';
    protected $description = 'Command 30 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
