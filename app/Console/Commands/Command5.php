<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command5 extends Command
{
    protected $signature = 'command5';
    protected $description = 'Command 5 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
