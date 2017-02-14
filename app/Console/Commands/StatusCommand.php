<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class StatusCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status {--p|packages=}';

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
        $this->git()->action('status', $this->options());
    }

}