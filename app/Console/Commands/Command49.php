<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command49 extends Command
{
    protected $signature = 'command49';
    protected $description = 'Command 49 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
