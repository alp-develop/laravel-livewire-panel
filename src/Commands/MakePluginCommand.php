<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakePluginCommand extends Command
{
    protected $signature   = 'panel:make-plugin {name : Plugin name in PascalCase}';
    protected $description = 'Generate a plugin for the panel';

    public function handle(): int
    {
        $raw       = $this->argument('name');
        $name      = is_string($raw) ? $raw : '';
        $className = Str::studly($name);
        $id        = Str::snake($name);

        $path = app_path("Plugins/{$className}Plugin.php");

        if (file_exists($path)) {
            $this->error("Plugin {$className}Plugin already exists.");
            return self::FAILURE;
        }

        $this->ensureDirectory(dirname($path));

        file_put_contents($path, StubResolver::resolve('plugin.php.stub', [
            '{{ class }}' => $className,
            '{{ id }}'    => $id,
        ]));

        $this->info("Plugin {$className}Plugin generated in {$path}");
        $this->line("Register the plugin in your AppServiceProvider:");
        $this->line("  \$this->app->make(PluginRegistry::class)->register({$className}Plugin::class);");

        return self::SUCCESS;
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
