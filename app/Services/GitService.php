<?php

namespace App\Services;


class GitService extends BaseService
{
    public function update($options = [])
    {

        $this->cmd([
            $this->rm(env('PROJECT') . '/vendor/ohiocms'),
            $this->cd(env('PROJECT')),
            'composer update',
            'git add --all',
            'git commit -am "composer update"',
            'git push origin master',
        ]);

        $this->postUpdate($options);
    }

    public function postUpdate($options = [])
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

    public function commit($package, $options)
    {
        $message = array_get($options, 'commit');

        if (!$message) {
            throw new \Exception('missing commit message');
        }

        $this->cmd([
            $this->cd($package),
            'git add --all',
            sprintf('git commit -am "%s"', $message),
            'git push origin master',
        ]);
    }

    public function fetch($package)
    {
        $this->cmd([
            $this->cd($package),
            'git pull origin master'
        ]);
    }

    public function status($package)
    {
        $this->cmd([
            $this->cd($package),
            'git status'
        ]);
    }

}