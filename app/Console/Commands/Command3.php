<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command3 extends Command
{
    protected $signature = 'command3';
    protected $description = 'Command 3 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
