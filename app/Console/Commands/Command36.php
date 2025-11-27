<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class Command36 extends Command
{
    protected $signature = 'command36';
    protected $description = 'Command 36 description';
    
    public function handle()
    {
        $this->info('Command executed');
    }
}
