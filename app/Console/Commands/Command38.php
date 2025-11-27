<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command38 extends Command
{
    protected $signature = 'command38';
    protected $description = 'Command 38 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
