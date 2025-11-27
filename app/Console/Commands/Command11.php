<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command11 extends Command
{
    protected $signature = 'command11';
    protected $description = 'Command 11 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
