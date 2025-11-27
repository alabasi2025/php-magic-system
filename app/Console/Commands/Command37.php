<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command37 extends Command
{
    protected $signature = 'command37';
    protected $description = 'Command 37 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
