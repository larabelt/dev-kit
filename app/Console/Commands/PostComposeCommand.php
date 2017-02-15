<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class PostComposeCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post-compose {--p|packages=} {--m|message=}';

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
        $this->git()->postCompose($this->options());
    }

}