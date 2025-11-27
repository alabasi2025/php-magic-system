<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command16 extends Command
{
    protected $signature = 'command16';
    protected $description = 'Command 16 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
