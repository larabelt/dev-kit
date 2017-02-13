<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class PhpUnitCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phpunit {action} 
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
        $service = new Services\PhpUnitService(['console' => $this]);

        $action = $this->argument('action');

        if ($action == 'test') {
            $packages = $this->option('packages') ? explode(',', $this->option('packages')) : $service->packages();
            foreach ($packages as $package) {
                $this->info("\n$package test:\n");
                $service->test($package, $this->options());
            }
        }

    }

}