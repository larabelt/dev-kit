<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class BranchCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'branch {action} {branch} {--p|packages=}';

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
        $options = $this->options();
        $options['branch'] = $this->argument('branch');

        $action = $this->argument('action');

        $this->git()->action($action, $options);
    }

}