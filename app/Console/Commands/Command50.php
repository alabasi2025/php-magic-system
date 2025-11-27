<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command50 extends Command
{
    protected $signature = 'command50';
    protected $description = 'Command 50 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
