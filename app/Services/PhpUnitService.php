<?php

namespace App\Services;


class PhpUnitService extends BaseService
{
    public function test($package, $options = [])
    {

        $filter = array_get($options, 'filter');
        $group = array_get($options, 'group');
        $suite = array_get($options, 'suite');
        $coverage = array_get($options, 'coverage');

        $testCmd = [
            'vendor/bin/phpunit',
            '--bootstrap=bootstrap/app.php',
            "-c ../$package/tests",
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
            $testCmd[] = "--coverage-html=public/tests/ohio/$package";
        }

        $testCmd = implode(' ', $testCmd);

//        return $this->cmd2(env('PROJECT'), [$testCmd]);

        $this->cmd([
            $this->cd(env('PROJECT')),
            $testCmd
        ]);
    }

}