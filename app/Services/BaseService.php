<?php

namespace App\Services;

use Illuminate\Console\Command;

class BaseService
{

    public $config;

    public $paths;

    public $packages;

    /**
     * @var Command
     */
    public $console;

    public function __construct($options = [])
    {
        $this->console = array_get($options, 'console');
        $this->config = $this->setConfig();
    }

    public function setConfig()
    {
        $path = base_path('cc.json');
        if (!file_exists($path)) {
            $path = base_path('cc.example.json');
        }

        $config = json_decode(file_get_contents($path), true);

        return $this->config = $config;
    }

    public function config($key = null, $default = false)
    {
        if ($key) {
            return array_get($this->config, $key, $default);
        }

        return $this->config;
    }

    public function paths()
    {
        // return active paths
        if (!is_null($this->paths)) {
            return $this->paths;
        }

        $config = $this->config();

        $paths = [];
        foreach ($this->packages as $package) {
            $paths[] = array_get($config, "packages.$package.path");
        }

        return $this->paths = $paths;
    }

    public function packages($packages = null)
    {
        // manually set active packages
        if ($packages) {
            return $this->packages = is_array($packages) ? $packages : explode(',', $packages);
        }

        // return active packages if set
        if (!is_null($this->packages)) {
            return $this->packages;
        }

        // automatically set active packages
        $packages = [];
        foreach (array_get($this->config(), 'packages', []) as $key => $package) {
            $packages[] = $key;
        }

        return $this->packages = $packages;
    }

    public function script($lines, $options = [])
    {
        $script = [];
        foreach ($lines as $line) {
            $line = str_replace('{root}', $this->path(), $line);
            $line = str_replace('{project}', env('PROJECT'), $line);
            $line = str_replace('{package}', array_get($options, 'package'), $line);
            $script[] = $line;
        }

        return $script;
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
        if (!$path) {
            return sprintf("%s/..", base_path());
        }

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