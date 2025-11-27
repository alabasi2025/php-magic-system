<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command35 extends Command
{
    protected $signature = 'command35';
    protected $description = 'Command 35 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
