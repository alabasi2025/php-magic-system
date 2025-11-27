<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command13 extends Command
{
    protected $signature = 'command13';
    protected $description = 'Command 13 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
