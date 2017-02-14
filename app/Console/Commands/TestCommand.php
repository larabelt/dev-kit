<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class TestCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test 
        {--p|packages=}
        {--f|filter=} 
        {--s|suite=} 
        {--g|group=} 
        {--c|coverage}
        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->phpunit()->test($this->options());

    }

}