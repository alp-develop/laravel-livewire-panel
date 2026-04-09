<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

final class InstallCommand extends Command
{
    protected $signature   = 'panel:install {--force : Overwrite existing files} {--defaults : Skip interactive prompts and use defaults}';
    protected $description = 'Install the laravel-livewire-panel package in your project';

    public function handle(): int
    {
        $this->info('Installing laravel-livewire-panel...');
        $this->newLine();

        $this->publishConfig();
        $this->publishStyles();
        $this->createDefaultStyle();

        if (!$this->option('defaults')) {
            $this->configurePanelInteractive();
        }

        $publishViews = !$this->option('defaults')
            ? confirm('Publish package views for customization?', false)
            : false;

        if ($publishViews) {
            $this->publishViews();
        }

        $this->newLine();
        $this->info('Installation completed.');

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        $this->callSilent('vendor:publish', [
            '--provider' => 'AlpDevelop\\LivewirePanel\\LivewirePanelServiceProvider',
            '--tag'      => 'panel-config',
            '--force'    => $this->option('force'),
        ]);

        $this->line('  Configuration published -> config/laravel-livewire-panel.php');
    }

    private function publishStyles(): void
    {
        $this->callSilent('vendor:publish', [
            '--provider' => 'AlpDevelop\\LivewirePanel\\LivewirePanelServiceProvider',
            '--tag'      => 'panel-styles',
            '--force'    => $this->option('force'),
        ]);

        $this->line('  Styles published -> config/laravel-livewire-panel/');
    }

    private function publishViews(): void
    {
        $this->callSilent('vendor:publish', [
            '--provider' => 'AlpDevelop\\LivewirePanel\\LivewirePanelServiceProvider',
            '--tag'      => 'panel-views',
            '--force'    => $this->option('force'),
        ]);

        $this->line('  Views published -> resources/views/vendor/panel/');
    }

    private function createDefaultStyle(): void
    {
        $this->ensureDirectory(config_path('laravel-livewire-panel'));

        $defaultStyle = config_path('laravel-livewire-panel/style_table.php');
        if (!file_exists($defaultStyle) || $this->option('force')) {
            file_put_contents($defaultStyle, $this->defaultStyleContent());
            $this->line('  Default style created -> config/laravel-livewire-panel/style_table.php');
        }
    }

    private function configurePanelInteractive(): void
    {
        $this->newLine();
        $this->info('Panel Configuration');
        $this->line('  Let\'s configure your main panel.');
        $this->newLine();

        $panelId = text(
            label: 'URL prefix',
            placeholder: 'admin',
            default: 'admin',
            hint: 'Route prefix for the panel (e.g. /admin)',
        );

        $navigationMode = select(
            label: 'Navigation mode',
            options: [
                'config'  => 'Config — Sidebar and Navbar defined manually in configuration array',
                'modules' => 'Modules — Sidebar and Navbar built automatically from registered modules',
            ],
            default: 'config',
        );

        $theme = select(
            label: 'CSS Theme',
            options: [
                'bootstrap5' => 'Bootstrap 5',
                'bootstrap4' => 'Bootstrap 4',
                'tailwind'   => 'Tailwind CSS',
            ],
            default: 'bootstrap5',
        );

        $gate = select(
            label: 'Gate driver (permission system)',
            options: [
                'null'    => 'None — No permission checks',
                'spatie'  => 'Spatie — Uses spatie/laravel-permission',
                'laravel' => 'Laravel — Uses Laravel\'s built-in Gate',
            ],
            default: 'null',
        );

        $registration = confirm('Enable user registration?', false);

        $cdnLibs = multiselect(
            label: 'CDN libraries to include',
            options: [
                'chartjs'     => 'Chart.js — Charts and graphs',
                'sweetalert2' => 'SweetAlert2 — Alert dialogs',
                'select2'     => 'Select2 — Enhanced selects',
                'flatpickr'   => 'Flatpickr — Date picker',
            ],
            default: ['chartjs'],
            hint: 'Space to select, Enter to confirm',
        );

        $this->writeConfig($panelId, $navigationMode, $theme, $gate, $registration, $cdnLibs);

        $this->newLine();
        $this->line('  Config updated with your choices.');
    }

    private function writeConfig(
        string $panelId,
        string $navigationMode,
        string $theme,
        string $gate,
        bool $registration,
        array $cdnLibs,
    ): void {
        $gateValue      = $gate === 'null' ? 'null' : var_export($gate, true);
        $regValue       = $registration ? 'true' : 'false';
        $cdnBlock       = $this->buildCdnBlock($cdnLibs);
        $navBlock       = $this->buildNavigationBlock($panelId, $navigationMode);

        $panels = $this->buildPanelBlock($panelId, $navigationMode, $theme, $gateValue, $regValue, $cdnBlock, $navBlock);

        $content = "<?php\n\ndeclare(strict_types=1);\n\nreturn [\n\n    'default' => '{$panelId}',\n\n    'panels' => [\n\n{$panels}\n\n    ],\n\n];\n";

        file_put_contents(config_path('laravel-livewire-panel.php'), $content);
    }

    private function buildPanelBlock(
        string $id,
        string $navigationMode,
        string $theme,
        string $gateValue,
        string $regValue,
        string $cdnBlock,
        string $navBlock,
    ): string {
        $lines = [];
        $lines[] = "        '{$id}' => [";
        $lines[] = "            'id'                   => '{$id}',";
        $lines[] = "            'prefix'               => '{$id}',";
        $lines[] = "            'guard'                => 'web',";
        $lines[] = "            'theme'                => '{$theme}',";
        $lines[] = "            'customization'        => 'style_table',";
        $lines[] = "            'middleware'           => ['web', 'auth'],";
        $lines[] = "            'gate'                 => {$gateValue},";
        $lines[] = "            'registration_enabled' => {$regValue},";
        $lines[] = "            'mode'                 => '{$navigationMode}',";
        $lines[] = $navBlock;
        $lines[] = "            'user_menu' => [],";
        $lines[] = "            'navbar_components' => ['left' => [], 'right' => []],";
        $lines[] = "            'locale' => [";
        $lines[] = "                'enabled'      => false,";
        $lines[] = "                'show_on_auth' => false,";
        $lines[] = "                'available'    => [";
        $lines[] = "                    'en' => 'English',";
        $lines[] = "                    'es' => 'Español',";
        $lines[] = "                    'fr' => 'Français',";
        $lines[] = "                ],";
        $lines[] = "            ],";
        $lines[] = "            'components' => [";
        $lines[] = "                'login'                        => null,";
        $lines[] = "                'register'                     => null,";
        $lines[] = "                'forgot-password'              => null,";
        $lines[] = "                'reset-password'               => null,";
        $lines[] = "                'forgot-password-notification' => null,";
        $lines[] = "                'sidebar'                      => null,";
        $lines[] = "                'navbar'                       => null,";
        $lines[] = "            ],";
        $lines[] = $cdnBlock;
        $lines[] = "        ],";

        return implode("\n", $lines);
    }

    private function buildNavigationBlock(string $panelId, string $navigationMode): string
    {
        return "            'sidebar_menu' => [],";
    }

    private function buildCdnBlock(array $libs): string
    {
        $cdnMap = [
            'chartjs' => [
                "                'chartjs' => [",
                "                    'css'    => [],",
                "                    'js'     => ['https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js'],",
                "                    'routes' => [],",
                "                ],",
            ],
            'sweetalert2' => [
                "                'sweetalert2' => [",
                "                    'css'    => ['https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'],",
                "                    'js'     => ['https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js'],",
                "                    'routes' => [],",
                "                ],",
            ],
            'select2' => [
                "                'select2' => [",
                "                    'css'    => ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],",
                "                    'js'     => [",
                "                        'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js',",
                "                        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',",
                "                    ],",
                "                    'routes' => [],",
                "                ],",
            ],
            'flatpickr' => [
                "                'flatpickr' => [",
                "                    'css'    => ['https://cdn.jsdelivr.net/npm/flatpickr@4.6/dist/flatpickr.min.css'],",
                "                    'js'     => ['https://cdn.jsdelivr.net/npm/flatpickr@4.6/dist/flatpickr.min.js'],",
                "                    'routes' => [],",
                "                ],",
            ],
        ];

        $entries = [];
        foreach ($libs as $lib) {
            if (isset($cdnMap[$lib])) {
                $entries = array_merge($entries, $cdnMap[$lib]);
            }
        }

        $lines = [];
        $lines[] = "            'cdn' => [";
        foreach ($entries as $entry) {
            $lines[] = $entry;
        }
        $lines[] = "            ],";

        return implode("\n", $lines);
    }

    private function defaultStyleContent(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        return [
            'colors' => [
                'primary'   => '#4f46e5',
                'secondary' => '#6c757d',
                'success'   => '#198754',
                'danger'    => '#dc3545',
                'warning'   => '#ffc107',
                'info'      => '#0dcaf0',
            ],
            'sidebar' => [
                'width'          => '260px',
                'collapsed_width'=> '64px',
                'background'     => '#1e293b',
                'color_scheme'   => 'dark',
            ],
            'navbar' => [
                'height' => '60px',
                'light' => [
                    'background'        => '#ffffff',
                    'text'              => '#1e293b',
                    'border'            => '#e2e8f0',
                    'icons_color'       => '#64748b',
                    'icons_hover_color' => '#334155',
                ],
                'dark' => [
                    'background'        => '#1e293b',
                    'text'              => '#e2e8f0',
                    'border'            => '#334155',
                    'icons_color'       => '#94a3b8',
                    'icons_hover_color' => '#e2e8f0',
                ],
            ],
            'typography' => [
                'font_family'   => 'sans-serif',
                'font_size'     => '14px',
                'border_radius' => '8px',
            ],
        ];
        PHP;
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
