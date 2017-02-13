<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class GitCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git {action}
        {--c|commit=}
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
        // definitions: branch, tag
        // graphic UI with bulma UI

        $service = new Services\GitService(['console' => $this]);

        $action = $this->argument('action');

        if ($action == 'commit') {
            foreach ($service->packages() as $package) {
                $this->info("\n$package commit:\n");
                $service->commit($package, $this->options());
            }
        }

        if ($action == 'fetch') {
            foreach ($service->packages() as $package) {
                $this->info("\n$package status:\n");
                $service->fetch($package, $this->options());
            }
        }

        if ($action == 'status') {
            foreach ($service->packages() as $package) {
                $this->info("\n$package status:\n");
                $service->status($package, $this->options());
            }
        }

        if ($action == 'update') {
            $this->info("\nproject update:\n");
            $service->update($this->options());
        }

        if ($action == 'post-update') {
            $this->info("\nproject post-update:\n");
            $service->postUpdate($this->options());
        }

    }

}