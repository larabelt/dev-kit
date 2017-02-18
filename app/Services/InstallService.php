<?php

namespace App\Services;

class InstallService extends BaseService
{
    public function install($options = [])
    {

        //git clones...
        //copy .env
        //key generate

        $this->packages(array_get($options, 'packages'));

        $project = env('PROJECT');

        # composer install
        $cmd = 'composer install';
        $this->cmd([
            $this->cd($project),
            $this->info("$cmd\n", 'blue'),
            $cmd
        ]);

        # pre-install (including publish)
        foreach ($this->packages() as $package) {
            $this->runScripts($package, 'pre-install');
        }

        # composer dumpautoload
        $cmd = 'composer dumpautoload';
        $this->cmd([
            $this->cd($project),
            $this->info("$cmd\n", 'blue'),
            $cmd
        ]);

        # migrate: refresh
        $cmd = 'php artisan migrate';
        $this->cmd([
            $this->cd($project),
            $this->info("$cmd\n", 'blue'),
            $cmd
        ]);

        # post-install (including seeds)
        foreach ($this->packages() as $package) {
            $this->runScripts($package, 'post-install');
        }

        # clear
        $cmd = 'composer run-script clear';
        $this->cmd([
            $this->cd($project),
            $this->info("$cmd\n", 'blue'),
            $cmd
        ]);

    }

    public function refresh($options = [])
    {

        $project = env('PROJECT');

        $force = array_get($options, 'force', false);

        # pre-install (including publish)
        foreach ($this->packages() as $package) {
            $this->runScripts($package, 'pre-install');
        }

        # composer dumpautoload
        $cmd = 'composer dumpautoload';
        $this->cmd([
            $this->cd($project),
            $this->info("$cmd\n", 'blue'),
            $cmd
        ]);

        # migrate: refresh
        $cmd = 'php artisan migrate:refresh';
        $this->cmd([
            $this->cd($project),
            $this->info("$cmd\n", 'blue'),
            $cmd
        ]);

        # post-install (including seeds)
        foreach ($this->packages() as $package) {
            $this->runScripts($package, 'pre-post');
        }

        # clear
        $cmd = 'composer run-script clear';
        $this->cmd([
            $this->cd($project),
            $this->info("$cmd\n", 'blue'),
            $cmd
        ]);

    }

}