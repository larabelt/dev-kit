<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class PostUpdateCommand extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post-update {--p|packages=} {--m|message=}';

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
        $this->git()->postUpdate($this->options());
    }

}