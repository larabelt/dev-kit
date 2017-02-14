<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class CommitCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commit {--p|packages=} {--m|message=}';

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
        $this->git()->action('commit', $this->options());
    }

}