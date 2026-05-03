<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Support\Facades\File;

final class ArtisanCommandTest extends PanelTestCase
{
    private array $generatedPaths = [];

    protected function tearDown(): void
    {
        foreach ($this->generatedPaths as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        $dirs = [
            app_path('Livewire/Widgets'),
            app_path('Livewire/Modules'),
            app_path('Livewire/Pages'),
            app_path('Livewire/Auth'),
            app_path('Livewire'),
            app_path('Themes'),
            app_path('Plugins'),
            resource_path('views/livewire/widgets'),
            resource_path('views/livewire/modules'),
            resource_path('views/livewire/pages'),
            resource_path('views/livewire/auth'),
            resource_path('views/livewire'),
            base_path('tests/Feature/Widgets'),
            base_path('config/laravel-livewire-panel'),
        ];

        foreach ($dirs as $dir) {
            if (is_dir($dir) && $this->isDirEmpty($dir)) {
                @rmdir($dir);
            }
        }

        parent::tearDown();
    }

    public function test_make_widget_generates_files(): void
    {
        $this->artisan('panel:make-widget', ['name' => 'TestStats'])
            ->assertSuccessful();

        $classPath = app_path('Livewire/Widgets/TestStats.php');
        $viewPath  = resource_path('views/livewire/widgets/test-stats.blade.php');

        $this->generatedPaths[] = $classPath;
        $this->generatedPaths[] = $viewPath;

        $this->assertFileExists($classPath);
        $this->assertFileExists($viewPath);

        $content = file_get_contents($classPath);
        $this->assertStringContainsString('namespace App\\Livewire\\Widgets', $content);
        $this->assertStringContainsString('final class TestStats extends AbstractWidget', $content);
        $this->assertStringContainsString('declare(strict_types=1)', $content);
    }

    public function test_make_widget_with_test_flag(): void
    {
        $this->artisan('panel:make-widget', ['name' => 'TestChart', '--test' => true])
            ->assertSuccessful();

        $classPath = app_path('Livewire/Widgets/TestChart.php');
        $viewPath  = resource_path('views/livewire/widgets/test-chart.blade.php');
        $testPath  = base_path('tests/Feature/Widgets/TestChartTest.php');

        $this->generatedPaths[] = $classPath;
        $this->generatedPaths[] = $viewPath;
        $this->generatedPaths[] = $testPath;

        $this->assertFileExists($testPath);
        $this->assertStringContainsString('Livewire::withoutLazyLoading()', file_get_contents($testPath));
    }

    public function test_make_widget_fails_if_exists(): void
    {
        $this->artisan('panel:make-widget', ['name' => 'DupeWidget'])
            ->assertSuccessful();

        $this->generatedPaths[] = app_path('Livewire/Widgets/DupeWidget.php');
        $this->generatedPaths[] = resource_path('views/livewire/widgets/dupe-widget.blade.php');

        $this->artisan('panel:make-widget', ['name' => 'DupeWidget'])
            ->assertFailed();
    }

    public function test_make_theme_generates_file(): void
    {
        $this->artisan('panel:make-theme', ['name' => 'TestCustom'])
            ->assertSuccessful();

        $classPath = app_path('Themes/TestCustomTheme.php');
        $this->generatedPaths[] = $classPath;

        $this->assertFileExists($classPath);

        $content = file_get_contents($classPath);
        $this->assertStringContainsString('namespace App\\Themes', $content);
        $this->assertStringContainsString('final class TestCustomTheme extends AbstractTheme', $content);
        $this->assertStringContainsString("return 'test_custom'", $content);
    }

    public function test_make_theme_fails_if_exists(): void
    {
        $this->artisan('panel:make-theme', ['name' => 'DupeTheme'])
            ->assertSuccessful();

        $this->generatedPaths[] = app_path('Themes/DupeThemeTheme.php');

        $this->artisan('panel:make-theme', ['name' => 'DupeTheme'])
            ->assertFailed();
    }

    public function test_make_plugin_generates_file(): void
    {
        $this->artisan('panel:make-plugin', ['name' => 'TestAnalytics'])
            ->assertSuccessful();

        $classPath = app_path('Plugins/TestAnalyticsPlugin.php');
        $this->generatedPaths[] = $classPath;

        $this->assertFileExists($classPath);

        $content = file_get_contents($classPath);
        $this->assertStringContainsString('namespace App\\Plugins', $content);
        $this->assertStringContainsString('final class TestAnalyticsPlugin extends AbstractPlugin', $content);
        $this->assertStringContainsString("return 'test_analytics'", $content);
    }

    public function test_make_plugin_fails_if_exists(): void
    {
        $this->artisan('panel:make-plugin', ['name' => 'DupePlugin'])
            ->assertSuccessful();

        $this->generatedPaths[] = app_path('Plugins/DupePluginPlugin.php');

        $this->artisan('panel:make-plugin', ['name' => 'DupePlugin'])
            ->assertFailed();
    }

    public function test_make_module_generates_files(): void
    {
        $this->artisan('panel:make-module', ['name' => 'TestReports'])
            ->assertSuccessful();

        $modulePath = app_path('Livewire/Modules/TestReports/TestReportsModule.php');
        $pagePath   = app_path('Livewire/Modules/TestReports/Pages/TestReportsPage.php');
        $viewPath   = resource_path('views/livewire/modules/test-reports/page.blade.php');

        $this->generatedPaths[] = $modulePath;
        $this->generatedPaths[] = $pagePath;
        $this->generatedPaths[] = $viewPath;

        $this->assertFileExists($modulePath);
        $this->assertFileExists($pagePath);
        $this->assertFileExists($viewPath);

        $content = file_get_contents($modulePath);
        $this->assertStringContainsString('final class TestReportsModule extends AbstractModule', $content);
        $this->assertStringContainsString("return 'test_reports'", $content);
    }

    public function test_make_page_generates_files(): void
    {
        $this->artisan('panel:make-page', ['name' => 'TestPricing'])
            ->assertSuccessful();

        $classPath = app_path('Livewire/Pages/TestPricingPage.php');
        $viewPath  = resource_path('views/livewire/pages/test-pricing.blade.php');

        $this->generatedPaths[] = $classPath;
        $this->generatedPaths[] = $viewPath;

        $this->assertFileExists($classPath);
        $this->assertFileExists($viewPath);

        $content = file_get_contents($classPath);
        $this->assertStringContainsString('namespace App\\Livewire\\Pages', $content);
        $this->assertStringContainsString("Layout('panel::layouts.public'", $content);
    }

    public function test_make_page_fails_if_exists(): void
    {
        $this->artisan('panel:make-page', ['name' => 'DupePage'])
            ->assertSuccessful();

        $this->generatedPaths[] = app_path('Livewire/Pages/DupePagePage.php');
        $this->generatedPaths[] = resource_path('views/livewire/pages/dupe-page.blade.php');

        $this->artisan('panel:make-page', ['name' => 'DupePage'])
            ->assertFailed();
    }

    public function test_make_component_login_generates_files(): void
    {
        $this->artisan('panel:make-component', ['type' => 'login', '--panel' => 'admin'])
            ->assertSuccessful();

        $classPath = app_path('Livewire/Auth/AdminLogin.php');
        $viewPath  = resource_path('views/livewire/auth/admin-login.blade.php');

        $this->generatedPaths[] = $classPath;
        $this->generatedPaths[] = $viewPath;

        $this->assertFileExists($classPath);
        $this->assertFileExists($viewPath);
    }

    public function test_make_component_rejects_invalid_type(): void
    {
        $this->artisan('panel:make-component', ['type' => 'invalid'])
            ->assertFailed();
    }

    public function test_make_style_generates_file(): void
    {
        $this->artisan('panel:make-style', ['name' => 'test-custom'])
            ->assertSuccessful();

        $path = config_path('laravel-livewire-panel/test-custom.php');
        $this->generatedPaths[] = $path;

        $this->assertFileExists($path);
        $this->assertStringContainsString('return [', file_get_contents($path));
    }

    public function test_make_notification_generates_file(): void
    {
        $this->artisan('panel:make-notification', ['name' => 'TestAlerts'])
            ->assertSuccessful();

        $classPath = app_path('Panel/Notifications/TestAlerts.php');
        $this->generatedPaths[] = $classPath;

        $this->assertFileExists($classPath);

        $content = file_get_contents($classPath);
        $this->assertStringContainsString('namespace App\\Panel\\Notifications', $content);
        $this->assertStringContainsString('NotificationProviderInterface', $content);
        $this->assertStringContainsString('declare(strict_types=1)', $content);
    }

    public function test_make_notification_fails_if_exists(): void
    {
        $this->artisan('panel:make-notification', ['name' => 'DupeNotification'])
            ->assertSuccessful();

        $this->generatedPaths[] = app_path('Panel/Notifications/DupeNotification.php');

        $this->artisan('panel:make-notification', ['name' => 'DupeNotification'])
            ->assertFailed();
    }

    public function test_list_panels_command_runs(): void
    {
        $this->artisan('panel:list')
            ->assertSuccessful();
    }

    private function isDirEmpty(string $dir): bool
    {
        $files = scandir($dir);
        return $files === false || count($files) <= 2;
    }
}
