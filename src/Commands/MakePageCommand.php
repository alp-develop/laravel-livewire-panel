<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakePageCommand extends Command
{
    protected $signature = 'panel:make-page
                            {name : Page name in PascalCase (e.g. Pricing, About, Terms)}
                            {--middleware=* : Additional middleware (e.g. --middleware=auth)}';

    protected $description = 'Generate a public Livewire page outside the panel (no auth, no sidebar)';

    public function handle(): int
    {
        $raw        = $this->argument('name');
        $name       = Str::studly(is_string($raw) ? $raw : '');
        $kebabName  = Str::kebab($name);
        $middleware  = (array) $this->option('middleware');
        $classPath  = app_path("Livewire/Pages/{$name}Page.php");
        $viewPath   = resource_path("views/livewire/pages/{$kebabName}.blade.php");

        if (file_exists($classPath)) {
            $this->error("Page {$name}Page already exists at {$classPath}");
            return Command::FAILURE;
        }

        $this->ensureDirectory(dirname($classPath));
        $this->ensureDirectory(dirname($viewPath));

        file_put_contents($classPath, $this->buildClass($name, $kebabName));
        file_put_contents($viewPath, $this->buildView($name));

        $this->info("Page {$name}Page generated:");
        $this->line("  PHP : {$classPath}");
        $this->line("  View: {$viewPath}");
        $this->newLine();

        $this->line('<comment>Option A:</comment> Auto-register via config/laravel-livewire-panel.php:');
        $this->newLine();
        $this->line("  'public_pages' => [");
        $this->line("      [");
        $this->line("          'route'     => '/{$kebabName}',");
        $this->line("          'component' => \\App\\Livewire\\Pages\\{$name}Page::class,");
        $this->line("          'name'      => '{$kebabName}',");

        if ($middleware !== []) {
            $mw = implode("', '", $middleware);
            $this->line("          'middleware' => ['{$mw}'],");
        }

        $this->line("      ],");
        $this->line("  ],");
        $this->newLine();

        $this->line('<comment>Option B:</comment> Manual route in routes/web.php:');
        $this->newLine();

        if ($middleware !== []) {
            $mw = implode("', '", $middleware);
            $this->line("  Route::middleware(['{$mw}'])");
            $this->line("      ->get('/{$kebabName}', \\App\\Livewire\\Pages\\{$name}Page::class)");
            $this->line("      ->name('{$kebabName}');");
        } else {
            $this->line("  Route::get('/{$kebabName}', \\App\\Livewire\\Pages\\{$name}Page::class)->name('{$kebabName}');");
        }

        return Command::SUCCESS;
    }

    private function buildClass(string $name, string $kebabName): string
    {
        return StubResolver::resolve('page.php.stub', [
            '{{ class }}'    => $name,
            '{{ viewName }}' => $kebabName,
        ]);
    }

    private function buildView(string $name): string
    {
        return "<div>\n    <div class=\"panel-page-header\">\n        <h1>{$name}</h1>\n    </div>\n</div>\n";
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
