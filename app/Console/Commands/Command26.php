<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command26 extends Command
{
    protected $signature = 'command26';
    protected $description = 'Command 26 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
