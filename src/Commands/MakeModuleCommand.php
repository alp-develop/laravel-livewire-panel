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
        $name      = (string) $this->argument('name');
        $className = Str::studly($name);
        $snakeName = Str::snake($name);
        $kebabName = Str::kebab($name);

        $this->generateModule($className, $snakeName);
        $this->generatePage($className, $snakeName, $kebabName);
        $this->generateView($className, $snakeName, $kebabName);

        $this->info("Module {$className} generated successfully.");
        $this->line("Register the module in your AppServiceProvider:");
        $this->line("  \$this->app->make(ModuleRegistry::class)->register('admin', {$className}Module::class);");

        return self::SUCCESS;
    }

    private function generateModule(string $className, string $snakeName): void
    {
        $path = app_path("Livewire/Modules/{$className}/{$className}Module.php");
        $this->ensureDirectory(dirname($path));

        file_put_contents($path, <<<PHP
        <?php

        declare(strict_types=1);

        namespace App\Livewire\Modules\\{$className};

        use AlpDevelop\LivewirePanel\Compat\LivewireCompat;
        use AlpDevelop\LivewirePanel\Http\Middleware\PanelAuthMiddleware;
        use AlpDevelop\LivewirePanel\Modules\AbstractModule;
        use AlpDevelop\LivewirePanel\Modules\NavigationItem;
        use App\Livewire\Modules\\{$className}\Pages\\{$className}Page;
        use Illuminate\Support\Facades\Route;

        final class {$className}Module extends AbstractModule
        {
            public function id(): string
            {
                return '{$snakeName}';
            }

            public function routes(): void
            {
                \$panelId = \$this->panelId();
                \$prefix  = \$this->prefix();

                Route::middleware(['web', PanelAuthMiddleware::class])
                    ->prefix(\$prefix . '/{$snakeName}')
                    ->name("panel.{\$panelId}.{$snakeName}.")
                    ->group(function () {
                        LivewireCompat::pageRoute('/', {$className}Page::class)->name('index');
                    });
            }

            public function navigationItems(): array
            {
                return [
                    new NavigationItem('{$className}', 'panel.' . \$this->panelId() . '.{$snakeName}.index', 'layer-group'),
                ];
            }

            public function permissions(): array
            {
                return [];
            }
        }
        PHP);
    }

    private function generatePage(string $className, string $snakeName, string $kebabName): void
    {
        $path = app_path("Livewire/Modules/{$className}/Pages/{$className}Page.php");
        $this->ensureDirectory(dirname($path));

        file_put_contents($path, <<<PHP
        <?php

        declare(strict_types=1);

        namespace App\Livewire\Modules\\{$className}\Pages;

        use Illuminate\Contracts\View\View;
        use Livewire\Attributes\Layout;
        use Livewire\Component;

        #[Layout('panel::layouts.app', ['title' => '{$className}'])]
        final class {$className}Page extends Component
        {
            public function render(): View
            {
                return view('livewire.modules.{$kebabName}.page');
            }
        }
        PHP);
    }

    private function generateView(string $className, string $snakeName, string $kebabName): void
    {
        $path = resource_path("views/livewire/modules/{$kebabName}/page.blade.php");
        $this->ensureDirectory(dirname($path));

        file_put_contents($path, <<<BLADE
        <div>
            <div class="panel-page-header">
                <h1>{$className}</h1>
            </div>

            <div class="panel-section-card">
                <div class="panel-section-header">Content</div>
                <div class="panel-section-body">
                    <p style="color:#64748b;font-size:.875rem">Module {$className} generated.</p>
                </div>
            </div>
        </div>
        BLADE);
    }

    private function ensureDirectory(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
