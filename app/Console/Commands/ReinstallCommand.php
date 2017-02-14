<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class ReinstallCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reinstall {--force}';

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
        $this->installer()->reinstall($this->options());
    }

}