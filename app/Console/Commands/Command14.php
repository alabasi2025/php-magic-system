<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command14 extends Command
{
    protected $signature = 'command14';
    protected $description = 'Command 14 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
