<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command21 extends Command
{
    protected $signature = 'command21';
    protected $description = 'Command 21 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
