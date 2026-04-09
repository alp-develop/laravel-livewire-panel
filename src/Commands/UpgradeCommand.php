<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;

final class UpgradeCommand extends Command
{
    protected $signature   = 'panel:upgrade {--force : Overwrite existing views and assets}';
    protected $description = 'Re-publish updated package assets and views';

    public function handle(): int
    {
        $this->info('Upgrading laravel-livewire-panel...');

        $this->callSilent('vendor:publish', [
            '--provider' => 'AlpDevelop\\LivewirePanel\\LivewirePanelServiceProvider',
            '--tag'      => 'panel-views',
            '--force'    => $this->option('force'),
        ]);

        $this->line('  Views updated → resources/views/vendor/panel/');

        $this->callSilent('view:clear');
        $this->line('  Compiled views cleared');

        $this->info('Upgrade completed.');

        return self::SUCCESS;
    }
}
