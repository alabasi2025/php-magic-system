<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command19 extends Command
{
    protected $signature = 'command19';
    protected $description = 'Command 19 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
