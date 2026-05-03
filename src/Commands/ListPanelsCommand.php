<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use AlpDevelop\LivewirePanel\Config\PanelConfig;
use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use Illuminate\Console\Command;

final class ListPanelsCommand extends Command
{
    protected $signature   = 'panel:list';
    protected $description = 'List all registered panels with their configuration';

    public function handle(): int
    {
        $config   = app(PanelConfig::class);
        $modules  = app(ModuleRegistry::class);
        $panels   = $config->all();
        $default  = $config->default();

        if (empty($panels)) {
            $this->warn('No panels registered.');
            return Command::SUCCESS;
        }

        $rows = [];

        foreach ($panels as $id => $panel) {
            $panelModules = $modules->forPanel($id);
            $moduleNames  = array_map(
                class_basename(...),
                $panelModules,
            );

            $rows[] = [
                $id . ($id === $default ? ' *' : ''),
                '/' . ltrim((string) ($panel['prefix'] ?? $id), '/'),
                (string) ($panel['theme'] ?? 'bootstrap5'),
                (string) ($panel['guard'] ?? 'web'),
                (string) ($panel['gate'] ?? 'null'),
                implode(', ', $moduleNames) ?: '-',
            ];
        }

        $this->table(
            ['ID', 'Prefix', 'Theme', 'Guard', 'Gate', 'Modules'],
            $rows,
        );

        $this->line('* = default panel');

        return Command::SUCCESS;
    }
}
