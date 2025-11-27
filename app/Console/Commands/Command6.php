<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command6 extends Command
{
    protected $signature = 'command6';
    protected $description = 'Command 6 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
