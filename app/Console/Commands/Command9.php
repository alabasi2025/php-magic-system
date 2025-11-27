<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command9 extends Command
{
    protected $signature = 'command9';
    protected $description = 'Command 9 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
