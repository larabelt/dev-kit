<?php

namespace App\Services;


class PhpUnitService extends BaseService
{
    public function test($options = [])
    {
        $paths = $this->packages(array_get($options, 'packages'));

        foreach ($paths as $path) {
            $this->cmd($this->info("\n$path\n", 'blue'));
            $this->__test($path, $options);
        }
    }

    public function __test($path, $options = [])
    {

        $filter = array_get($options, 'filter');
        $group = array_get($options, 'group');
        $suite = array_get($options, 'suite');
        $coverage = array_get($options, 'coverage');

        $testCmd = [
            'vendor/bin/phpunit',
            '--bootstrap=bootstrap/app.php',
            "-c ../$path/tests",
        ];

        if ($filter) {
            $testCmd[] = sprintf('--filter="%s"', $filter);
        }

        if ($group) {
            $testCmd[] = sprintf('--group="%s"', $group);
        }

        if ($suite) {
            $testCmd[] = sprintf('--testsuite="%s"', $suite);
        }

        if ($coverage) {
            $testCmd[] = "--coverage-html=public/tests/ohio/$path";
        }

        $testCmd = implode(' ', $testCmd);

        $this->cmd([
            $this->cd(env('PROJECT')),
            $testCmd
        ]);
    }

}