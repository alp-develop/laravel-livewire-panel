<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeThemeCommand extends Command
{
    protected $signature   = 'panel:make-theme {name : Theme name in PascalCase}';
    protected $description = 'Generate a custom theme class for the panel';

    public function handle(): int
    {
        $raw       = $this->argument('name');
        $name      = is_string($raw) ? $raw : '';
        $className = Str::studly($name) . 'Theme';
        $id        = Str::snake($name);
        $path      = app_path("Themes/{$className}.php");

        if (file_exists($path)) {
            $this->error("Theme {$className} already exists.");
            return self::FAILURE;
        }

        $this->ensureDirectory(dirname($path));
        file_put_contents($path, StubResolver::resolve('theme.php.stub', [
            '{{ class }}' => $className,
            '{{ id }}'    => $id,
        ]));

        $this->info("Theme created → app/Themes/{$className}.php");
        $this->line("Register it in AppServiceProvider:");
        $this->line("  \$themeRegistry->register('{$id}', {$className}::class);");
        $this->line("Use it in config/laravel-livewire-panel.php:");
        $this->line("  'theme' => '{$id}'");

        return self::SUCCESS;
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
