<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeWidgetCommand extends Command
{
    protected $signature = 'panel:make-widget {name : Widget name in PascalCase} {--test : Generate a Pest test file}';

    protected $description = 'Generate a new widget for the panel';

    public function handle(): int
    {
        $name      = (string) $this->argument('name');
        $className = Str::studly($name);
        $viewName  = Str::kebab($name);

        $classPath = app_path("Livewire/Widgets/{$className}.php");
        $viewPath  = resource_path("views/livewire/widgets/{$viewName}.blade.php");

        if (file_exists($classPath)) {
            $this->error("Widget {$className} already exists.");
            return Command::FAILURE;
        }

        $namespace = 'App\\Livewire\\Widgets';
        $classContent = <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$namespace};

        use AlpDevelop\LivewirePanel\Widgets\AbstractWidget;
        use Illuminate\Contracts\View\View;

        final class {$className} extends AbstractWidget
        {
            public function render(): View
            {
                return view('livewire.widgets.{$viewName}');
            }
        }
        PHP;

        $classContent = implode("\n", array_map(
            fn (string $line) => ltrim($line),
            explode("\n", $classContent),
        ));

        $viewContent = <<<BLADE
        <x-panel::card :title="\$title">
            {{-- Widget {$className} content --}}
        </x-panel::card>
        BLADE;

        $viewContent = implode("\n", array_map(
            fn (string $line) => ltrim($line),
            explode("\n", $viewContent),
        ));

        $this->ensureDirectory(dirname($classPath));
        $this->ensureDirectory(dirname($viewPath));

        file_put_contents($classPath, $classContent);
        file_put_contents($viewPath, $viewContent);

        $this->info("Widget created:");
        $this->line("  PHP : {$classPath}");
        $this->line("  View: {$viewPath}");

        if ($this->option('test')) {
            $this->generateTest($className, $namespace, $viewName);
        }

        return Command::SUCCESS;
    }

    private function generateTest(string $className, string $namespace, string $viewName): void
    {
        $testPath = base_path("tests/Feature/Widgets/{$className}Test.php");

        $testContent = <<<PHP
        <?php

        declare(strict_types=1);

        namespace Tests\Feature\Widgets;

        use {$namespace}\\{$className};
        use Livewire\Livewire;
        use Tests\TestCase;

        final class {$className}Test extends TestCase
        {
            public function test_it_renders(): void
            {
                Livewire::withoutLazyLoading()->test({$className}::class, [
                    'title' => 'Test Widget',
                ])
                    ->assertSee('Test Widget')
                    ->assertStatus(200);
            }

            public function test_can_view_returns_true(): void
            {
                Livewire::withoutLazyLoading();
                \$component = Livewire::test({$className}::class, ['title' => 'Test']);
                \$this->assertTrue(\$component->instance()->canView());
            }
        }
        PHP;

        $testContent = implode("\n", array_map(
            fn (string $line) => ltrim($line),
            explode("\n", $testContent),
        ));

        $this->ensureDirectory(dirname($testPath));
        file_put_contents($testPath, $testContent);

        $this->line("  Test: {$testPath}");
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
