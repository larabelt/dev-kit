<?php

namespace App\Services;


class GitService extends BaseService
{

    public function action($action, $options = [])
    {

        $this->packages(array_get($options, 'packages'));

        if (!array_get($options, 'packages')) {
            $path = env('PROJECT');
            $this->cmd($this->info("\n$path\n", 'blue'));
            $this->$action($path, $options);
        }

        foreach ($this->packages() as $package) {
            $path = $this->config("packages.$package.path");
            $this->cmd($this->info("\n$package\n", 'blue'));
            $this->$action($path, $options);
        }
    }

    public function push($path, $options)
    {
        $message = array_get($options, 'message');

        if (!$message) {
            throw new \Exception('missing commit message');
        }

        $this->cmd([
            $this->cd($path),
            'git add --all',
            sprintf('git commit -am "%s"', $message),
            'git push origin master',
        ]);
    }

    public function pull($path)
    {
        $this->cmd([
            $this->cd($path),
            'git pull origin master'
        ]);
    }

    public function status($path)
    {
        $this->cmd([
            $this->cd($path),
            'git status'
        ]);
    }

    public function compose($options = [])
    {
        $message = array_get($options, 'message') ?: 'composer update';

        $this->cmd([
            $this->rm(env('PROJECT') . '/vendor/larabelt'),
            $this->cd(env('PROJECT')),
            'composer update',
            'git add --all',
            "git commit -am '$message'",
            'git push origin master',
        ]);

        $this->postCompose();
    }

    public function postCompose()
    {
        $project = env('PROJECT');

        // project specific
        $this->cmd([
            $this->rm("$project/vendor/larabelt"),
            //$this->rm("$project/resources/belt"),
            $this->mkdir("$project/vendor/larabelt"),
            //$this->mkdir("$project/resources/belt"),
        ]);

        foreach ($this->packages() as $package) {
            $this->runScripts($package, 'post-compose');
        }

//        foreach ($this->packages() as $package) {
//            $scripts = $this->config("packages.$package.scripts.post-compose");
//            if ($scripts) {
//                foreach ($scripts as $script) {
//                    if (is_string($script)) {
//                        $script = $this->config("scripts.$script");
//                    }
//                    $cmd = $this->script($script, ['package' => $package]);
//                    $this->cmd($cmd);
//                }
//            }
//        }
    }

}