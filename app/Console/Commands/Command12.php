<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command12 extends Command
{
    protected $signature = 'command12';
    protected $description = 'Command 12 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
