<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command27 extends Command
{
    protected $signature = 'command27';
    protected $description = 'Command 27 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
