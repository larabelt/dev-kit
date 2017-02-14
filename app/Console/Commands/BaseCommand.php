<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services;

class BaseCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function git()
    {
        return new Services\GitService(['console' => $this]);
    }

    public function phpunit()
    {
        return new Services\PhpUnitService(['console' => $this]);
    }

    public function installer()
    {
        return new Services\InstallService(['console' => $this]);
    }

}