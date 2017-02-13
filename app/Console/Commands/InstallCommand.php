<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class InstallCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install
        {--force}
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
        $service = new Services\InstallService(['console' => $this]);

        $service->install($this->options());

    }

}