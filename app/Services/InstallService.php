<?php

namespace App\Services;


use Illuminate\Support\Str;

class InstallService extends BaseService
{
    public function install($options)
    {

        $cmds = [$this->cd(env('PROJECT'))];

        # publish
        $cmds[] = $this->info("\n#publish\n", 'blue');
        foreach ($this->packages() as $package) {
            $force = array_get($options, 'force') ? '--force' : '';
            $cmds[] = sprintf('php artisan ohio-%s:publish %s', $package, $force);
        }
        $cmds[] = 'composer dumpautoload';

        # migrate & seed
        $cmds[] = $this->info("\n#migrate\n", 'blue');
        $cmds[] = 'php artisan migrate:refresh';
        $cmds[] = $this->info("\n#seed\n", 'blue');
        foreach ($this->packages() as $package) {
            $class = sprintf('Ohio%sSeeder', Str::title($package));
            $file = sprintf('%s/../%s/database/seeds/%s.php', base_path(), env('PROJECT'), $class);
            if (file_exists($file)) {
                $cmds[] = sprintf('php artisan db:seed --class=Ohio%sSeeder', Str::title($package));
            }
        }
        $cmds[] = 'composer run-script clear';

        return $this->cmd($cmds);
    }

}