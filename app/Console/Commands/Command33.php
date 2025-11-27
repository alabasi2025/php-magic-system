<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command33 extends Command
{
    protected $signature = 'command33';
    protected $description = 'Command 33 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
