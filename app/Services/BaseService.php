<?php

namespace App\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BaseService
{

    public $config;

    public $paths;

    public $packages;

    public $branch = 'master';

    public $writeConfig = false;

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

    public function writeConfig()
    {
        $path = base_path('cc.json');

        $content = json_encode($this->config, JSON_PRETTY_PRINT);

        file_put_contents($path, $content);
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

    public function branch($branch = null, $config_path)
    {
        $this->branch = $branch ?: $this->config($config_path, 'master');

        array_set($this->config, $config_path, $branch);
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

        $project = env('PROJECT');
        $package = array_get($options, 'package');

        $script = [];
        foreach ($lines as $line) {
            $line = str_replace('{root}', $this->path(), $line);
            $line = str_replace('{project}', $project, $line);
            $line = str_replace('{package}', $package, $line);
            $line = str_replace('{seeder}', sprintf('php artisan db:seed --class=Belt%sSeeder', Str::title($package)), $line);
            $script[] = $line;
        }

        return $script;
    }

    public function scriptReplace($lines, $options = [])
    {
        $script = [];
        foreach ($lines as $line) {
            $line = str_replace('{root}', $this->path(), $line);
            $line = str_replace('{project}', env('PROJECT'), $line);
            $line = str_replace('{package}', array_get($options, 'package'), $line);
            $line = str_replace('{seeder}', array_get($options, 'package'), $line);
            $script[] = $line;
        }

        return $script;
    }

    public function runScripts($package, $type, $options = [])
    {
        $scripts = $this->config("packages.$package.scripts.$type");
        if ($scripts) {
            foreach ($scripts as $script) {
                if (is_string($script)) {
                    $script = $this->config("scripts.$script");
                }
                $_options = array_merge($options, ['package' => $package]);
                $cmd = $this->scriptReplace($script, $_options);
                $this->cmd($cmd);
            }
        }
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