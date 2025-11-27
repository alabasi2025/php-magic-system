<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command18 extends Command
{
    protected $signature = 'command18';
    protected $description = 'Command 18 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
