<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class TagCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag {--p|packages=} {--t|tag=} {--m|message=} {--d|delete}';

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
        $this->git()->action('tag', $this->options());
    }

}