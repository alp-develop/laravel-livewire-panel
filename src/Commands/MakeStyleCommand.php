<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeStyleCommand extends Command
{
    protected $signature   = 'panel:make-style {name : Style name (e.g.: client, dark, orange)}';
    protected $description = 'Generate a panel customization file in config/laravel-livewire-panel/';

    public function handle(): int
    {
        $name     = Str::slug((string) $this->argument('name'));
        $path     = config_path("laravel-livewire-panel/{$name}.php");

        if (file_exists($path)) {
            $this->error("Style '{$name}' already exists in config/laravel-livewire-panel/{$name}.php");
            return self::FAILURE;
        }

        $this->ensureDirectory(dirname($path));
        file_put_contents($path, $this->content($name));

        $this->info("Style created → config/laravel-livewire-panel/{$name}.php");
        $this->line("Use it in config/laravel-livewire-panel.php:");
        $this->line("  'customization' => '{$name}'");

        return self::SUCCESS;
    }

    private function content(string $name): string
    {
        return <<<PHP
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
                'width'           => '260px',
                'collapsed_width' => '64px',
                'background'      => '#1e293b',
                'color_scheme'    => 'dark',
                'text_color'      => '#cbd5e1',
                'muted_color'     => '#64748b',
            ],
            'navbar' => [
                'height'       => '60px',
                'background'   => '#1e293b',
                'text_color'   => '#e2e8f0',
                'border_color' => '#334155',
            ],
            'content' => [
                'background'      => '#0f172a',
                'card_background' => '#1e293b',
                'card_border'     => '#334155',
                'text_primary'    => '#e2e8f0',
                'text_muted'      => '#64748b',
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
