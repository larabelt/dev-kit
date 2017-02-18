<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class InstallCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install {--p|packages=}';

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
        $this->installer()->install($this->options());
    }

}