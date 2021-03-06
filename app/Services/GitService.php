<?php

namespace App\Services;


class GitService extends BaseService
{

    public function action($action, $options = [])
    {

        $this->packages(array_get($options, 'packages'));

        if (!array_get($options, 'packages')) {
            $path = env('PROJECT');
            $this->branch(array_get($options, 'branch'), 'project.branch');
            $this->cmd($this->info("\n$path\n", 'blue'));
            $this->$action($path, $options);
        }

        foreach ($this->packages() as $package) {
            $this->branch(array_get($options, 'branch'), "packages.$package.branch");
            $path = $this->config("packages.$package.path");
            $this->cmd($this->info("\n$package\n", 'blue'));
            $this->$action($path, $options);
        }

        if ($this->writeConfig) {
            $this->writeConfig();
        }
    }

    public function checkout($path, $options)
    {
        $this->writeConfig = true;

        $this->cmd([
            $this->cd($path),
            sprintf('git branch %s', $this->branch),
            sprintf('git checkout %s', $this->branch),
        ]);
    }

    public function createBranch($path, $options)
    {
        $this->cmd([
            $this->cd($path),
            'git checkout master',
            sprintf('git checkout -b %s', $options['branch']),
        ]);
    }

    public function deleteBranch($path, $options)
    {
        $this->cmd([
            $this->cd($path),
            'git checkout master',
            sprintf('git branch -D %s', $options['branch']),
            sprintf('git push origin :%s', $options['branch']),
        ]);
    }

    public function pull($path)
    {

        $version = $this->config('version');

        $cmds = [
            $this->cd($path),
            'git fetch origin',
            sprintf('git checkout %s', $this->branch),
            sprintf('git pull origin %s', $this->branch),
        ];

        if ($version) {
            //$cmds[] = sprintf('git checkout %s', $version);
        }

        $this->cmd($cmds);
    }

    public function push($path, $options)
    {
        $message = array_get($options, 'message');

        if (!$message) {
            throw new \Exception('missing commit message');
        }

        $this->cmd([
            $this->cd($path),
            sprintf('git checkout %s', $this->branch),
            'git add --all',
            sprintf('git commit -am "%s"', $message),
            sprintf('git push origin %s', $this->branch),
        ]);
    }

    public function tag($path, $options)
    {
        $delete = array_get($options, 'delete');
        $version = array_get($options, 'tag');

        if (!$version) {
            throw new \Exception('missing tag version');
        }

        if ($delete) {
            $this->cmd([
                $this->cd($path),
                sprintf('git tag -d %s', $version),
            ]);
            $this->cmd([
                $this->cd($path),
                //sprintf('git push origin :refs/tags/%s', $version),
                sprintf('git push --delete origin %s', $version),
            ]);
        } else {
            $this->cmd([
                $this->cd($path),
                sprintf('git tag %s', $version),
            ]);
            $this->cmd([
                $this->cd($path),
                sprintf('git push origin --tags', $version),
            ]);
        }

    }

    public function status($path)
    {
        $this->cmd([
            $this->cd($path),
            sprintf('git checkout %s', $this->branch),
            'git status'
        ]);
    }

    public function compose($options = [])
    {
        foreach ($this->packages() as $package) {
            $this->runScripts($package, 'compose');
        }

        $message = array_get($options, 'message') ?: 'composer update';

        $this->cmd([
            $this->rm(env('PROJECT') . '/vendor/larabelt'),
            $this->cd(env('PROJECT')),
            'composer update',
            'git add --all',
            "git commit -am '$message'",
            sprintf('git push origin %s', $this->branch),
        ]);

        $this->postCompose();
    }

    public function postCompose()
    {
        $project = env('PROJECT');

        // project specific
        $this->cmd([
            $this->rm("$project/vendor/larabelt"),
            $this->mkdir("$project/vendor/larabelt"),
        ]);

        foreach ($this->packages() as $package) {
            $this->runScripts($package, 'post-compose');
        }

    }

}