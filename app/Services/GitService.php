<?php

namespace App\Services;


class GitService extends BaseService
{

    public function action($action, $options = [])
    {

        $paths = $this->packages(array_get($options, 'packages'));

        if (!array_get($options, 'packages')) {
            $paths[] = env('PROJECT');
        }

        foreach ($paths as $path) {
            $this->cmd($this->info("\n$path\n", 'blue'));
            $this->$action($path, $options);
        }

    }

    public function commit($path, $options)
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

    public function fetch($path)
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

    public function update($options = [])
    {
        $message = array_get($options, 'message', 'composer update');

        $this->cmd([
            $this->rm(env('PROJECT') . '/vendor/ohiocms'),
            $this->cd(env('PROJECT')),
            'composer update',
            'git add --all',
            "git commit -am '$message'",
            'git push origin master',
        ]);

        $this->postUpdate();
    }

    public function postUpdate()
    {
        $project = env('PROJECT');

        $this->cmd([
            $this->rm("$project/vendor/ohiocms"),
            $this->rm("$project/resources/ohio"),
            $this->mkdir("$project/vendor/ohiocms"),
            $this->mkdir("$project/resources/ohio"),
        ]);

        foreach ($this->packages() as $package) {
            $this->cmd([
                $this->cd("$project/vendor/ohiocms"),
                $this->symlink("../../../$package", $package),
                $this->cd("$project/resources/ohio"),
                $this->symlink("../../../$package/resources", $package),
                $this->cd($package),
                $this->rm("$package/.babelrc"),
                $this->rm("$package/node_modules"),
                $this->symlink("../$project/.babelrc", '.babelrc'),
                $this->symlink("../$project/node_modules", 'node_modules'),
            ]);
        }
    }

}