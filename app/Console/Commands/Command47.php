<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command47 extends Command
{
    protected $signature = 'command47';
    protected $description = 'Command 47 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
