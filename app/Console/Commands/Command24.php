<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command24 extends Command
{
    protected $signature = 'command24';
    protected $description = 'Command 24 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
