<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command28 extends Command
{
    protected $signature = 'command28';
    protected $description = 'Command 28 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
