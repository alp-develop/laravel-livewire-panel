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
        $name      = (string) $this->argument('name');
        $className = Str::studly($name) . 'Theme';
        $id        = Str::snake($name);
        $path      = app_path("Themes/{$className}.php");

        if (file_exists($path)) {
            $this->error("Theme {$className} already exists.");
            return self::FAILURE;
        }

        $this->ensureDirectory(dirname($path));
        file_put_contents($path, $this->content($className, $id));

        $this->info("Theme created → app/Themes/{$className}.php");
        $this->line("Register it in AppServiceProvider:");
        $this->line("  \$themeRegistry->register('{$id}', {$className}::class);");
        $this->line("Use it in config/laravel-livewire-panel.php:");
        $this->line("  'theme' => '{$id}'");

        return self::SUCCESS;
    }

    private function content(string $className, string $id): string
    {
        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace App\Themes;

        use AlpDevelop\LivewirePanel\Themes\AbstractTheme;

        final class {$className} extends AbstractTheme
        {
            public function id(): string
            {
                return '{$id}';
            }

            public function cssAssets(): array
            {
                return [
                    // 'https://cdn.example.com/my-theme.css',
                ];
            }

            public function jsAssets(): array
            {
                return [
                    // 'https://cdn.example.com/my-theme.js',
                ];
            }

            public function componentClasses(): array
            {
                return [
                    'button' => ['root' => 'btn btn-primary'],
                    'card'   => ['root' => 'card', 'body' => 'card-body', 'title' => 'card-title'],
                    'alert'  => ['root' => 'alert'],
                ];
            }
        }
        PHP;
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
