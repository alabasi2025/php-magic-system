<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command15 extends Command
{
    protected $signature = 'command15';
    protected $description = 'Command 15 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
