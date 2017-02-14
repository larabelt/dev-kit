<?php

namespace App\Services;

use Illuminate\Support\Str;

class InstallService extends BaseService
{
    public function reinstall($options = [])
    {

        $project = env('PROJECT');

        $force = array_get($options, 'force', false);

        # publish
        foreach ($this->packages() as $package) {
            $cmd = sprintf('php artisan ohio-%s:publish %s', $package, $force ? '--force' : '');
            $this->cmd([
                $this->cd($project),
                $this->info($cmd, 'blue'),
                $cmd
            ]);
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

        # seeds
        foreach ($this->packages() as $package) {
            $class = sprintf('Ohio%sSeeder', Str::title($package));
            $file = sprintf('%s/../%s/database/seeds/%s.php', base_path(), $project, $class);
            if (file_exists($file)) {
                $cmd = sprintf('php artisan db:seed --class=Ohio%sSeeder', Str::title($package));
                $this->cmd([
                    $this->cd($project),
                    $cmd
                ]);
            }
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