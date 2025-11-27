<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command46 extends Command
{
    protected $signature = 'command46';
    protected $description = 'Command 46 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
