<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command22 extends Command
{
    protected $signature = 'command22';
    protected $description = 'Command 22 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
