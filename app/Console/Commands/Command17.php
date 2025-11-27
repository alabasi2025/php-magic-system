<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command17 extends Command
{
    protected $signature = 'command17';
    protected $description = 'Command 17 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
