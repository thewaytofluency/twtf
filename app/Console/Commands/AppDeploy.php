<?php

namespace App\Console\Commands;

use App\Support\Deployer;
use Illuminate\Console\Command;

class AppDeploy extends Command
{
    protected $signature = 'app:deploy {--demo : Also load the demo content (first run only)}';

    protected $description = 'Run the release bootstrap: storage folders, migrations and first-run data (safe to repeat)';

    public function handle(Deployer $deployer): int
    {
        foreach ($deployer->run($this->option('demo') || config('deploy.demo')) as $line) {
            $this->line($line);
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
