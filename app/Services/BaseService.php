<?php

namespace App\Services;

use Illuminate\Console\Command;

class BaseService
{

    public $packages;

    /**
     * @var Command
     */
    public $console;

    public function __construct($options = [])
    {
        $this->console = array_get($options, 'console');
    }

    public function packages($packages = null)
    {

        if ($packages) {
            return $this->packages = is_array($packages) ? $packages : explode(',', $packages);
        }

        if (!is_null($this->packages)) {
            return $this->packages;
        }

        // get composer.json contents
        $path = sprintf('%s/../%s/composer.json', base_path(), env('PROJECT'));
        $composer = json_decode(file_get_contents($path), true);

        $packages = [];
        foreach (array_get($composer, 'require') as $package => $version) {
            if (str_contains($package, 'larabelt/')) {
                $packages[] = str_replace('larabelt/', '', $package);
            }
        }
        foreach (array_get($composer, 'require-dev') as $package => $version) {
            if (str_contains($package, 'larabelt/')) {
                $packages[] = str_replace('larabelt/', '', $package);
            }
        }

        return $this->packages = $packages;
    }

    public function cmd($cmds)
    {
        $cmd = is_array($cmds) ? implode(';', $cmds) : $cmds;

        if ($this->console) {
            $this->liveExec($cmd);
        } else {
            $this->liveExec($cmd);
        }
    }

//    public function cmd2($path, $cmds)
//    {
//        foreach ($cmds as $cmd) {
//            return $this->cmd([
//                $this->cd($path),
//                $cmd
//            ]);
//        }
//    }

    function liveExec($cmd)
    {

        while (@ ob_end_flush()) {
            ;
        } // end all output buffers if any

        $proc = popen($cmd, 'r');

        while (!feof($proc)) {
            echo fread($proc, 4096);
            @ flush();
        }

        pclose($proc);
    }


    public function info($str, $foreground = null, $background = null)
    {

        if ($foreground || $background) {

            $prefix = '';

            if ($foreground) {
                $prefix .= Color::get($foreground);
            }

            if ($background) {
                $prefix .= Color::get($background);
            }
            $str = $prefix . $str . "\033[0m";
        }

        return sprintf('echo "%s"', $str);
    }

    public function path($path = '')
    {
        return sprintf("%s/../%s", base_path(), $path);
    }

    public function cd($path)
    {
        return sprintf("cd %s", $this->path($path));
    }

    public function rm($path)
    {
        return sprintf("rm -rf %s", $this->path($path));
    }

    public function mkdir($path)
    {
        return sprintf("mkdir %s", $this->path($path));
    }

    public function symlink($real, $symbolic)
    {
        return sprintf("ln -s %s %s", $real, $symbolic);
    }

}