<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command10 extends Command
{
    protected $signature = 'command10';
    protected $description = 'Command 10 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
