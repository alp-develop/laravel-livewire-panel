<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeNotificationCommand extends Command
{
    protected $signature = 'panel:make-notification {name : Provider class name in PascalCase}';

    protected $description = 'Generate a new notification provider for the panel';

    public function handle(): int
    {
        $raw       = $this->argument('name');
        $name      = is_string($raw) ? $raw : '';
        $className = Str::studly($name);
        $classPath = app_path("Panel/Notifications/{$className}.php");

        if (file_exists($classPath)) {
            $this->error("Notification provider {$className} already exists.");
            return Command::FAILURE;
        }
        $classContent = StubResolver::resolve('notification.php.stub', [
            '{{ class }}' => $className,
        ]);

        $this->ensureDirectory(dirname($classPath));

        file_put_contents($classPath, $classContent);

        $this->info("Notification provider created:");
        $this->line("  PHP: {$classPath}");
        $this->newLine();
        $this->line("Register it in a service provider:");
        $this->line("  app(NotificationRegistry::class)->register('{panelId}', new {$className}());");

        return Command::SUCCESS;
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
