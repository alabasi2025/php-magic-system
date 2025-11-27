<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command42 extends Command
{
    protected $signature = 'command42';
    protected $description = 'Command 42 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
