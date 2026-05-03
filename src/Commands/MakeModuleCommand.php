<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeModuleCommand extends Command
{
    protected $signature   = 'panel:make-module {name : Module name in PascalCase}';
    protected $description = 'Generate a new module for the panel';

    public function handle(): int
    {
        $raw       = $this->argument('name');
        $name      = is_string($raw) ? $raw : '';
        $className = Str::studly($name);
        $snakeName = Str::snake($name);
        $kebabName = Str::kebab($name);

        $this->generateModule($className, $snakeName);
        $this->generatePage($className, $kebabName);
        $this->generateView($className, $kebabName);

        $this->info("Module {$className} generated successfully.");
        $this->line("Register the module in your AppServiceProvider:");
        $this->line("  \$this->app->make(ModuleRegistry::class)->register('admin', {$className}Module::class);");

        return self::SUCCESS;
    }

    private function generateModule(string $className, string $snakeName): void
    {
        $path = app_path("Livewire/Modules/{$className}/{$className}Module.php");
        $this->ensureDirectory(dirname($path));

        file_put_contents($path, StubResolver::resolve('module.php.stub', [
            '{{ class }}'     => $className,
            '{{ snakeName }}' => $snakeName,
        ]));
    }

    private function generatePage(string $className, string $kebabName): void
    {
        $path = app_path("Livewire/Modules/{$className}/Pages/{$className}Page.php");
        $this->ensureDirectory(dirname($path));

        file_put_contents($path, StubResolver::resolve('module.page.php.stub', [
            '{{ class }}'     => $className,
            '{{ kebabName }}' => $kebabName,
        ]));
    }

    private function generateView(string $className, string $kebabName): void
    {
        $path = resource_path("views/livewire/modules/{$kebabName}/page.blade.php");
        $this->ensureDirectory(dirname($path));

        file_put_contents($path, "<div>\n    <div class=\"panel-page-header\">\n        <h1>{$className}</h1>\n    </div>\n\n    <div class=\"panel-section-card\">\n        <div class=\"panel-section-header\">Content</div>\n        <div class=\"panel-section-body\">\n            <p style=\"color:#64748b;font-size:.875rem\">Module {$className} generated.</p>\n        </div>\n    </div>\n</div>\n");
    }

    private function ensureDirectory(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
