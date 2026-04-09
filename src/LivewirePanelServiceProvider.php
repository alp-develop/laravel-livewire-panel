<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel;

use AlpDevelop\LivewirePanel\Auth\PanelAccessRegistry;
use AlpDevelop\LivewirePanel\Auth\PanelGate;
use AlpDevelop\LivewirePanel\Cdn\CdnManager;
use AlpDevelop\LivewirePanel\Cdn\CdnPluginResolver;
use AlpDevelop\LivewirePanel\Commands\InstallCommand;
use AlpDevelop\LivewirePanel\Commands\MakeComponentCommand;
use AlpDevelop\LivewirePanel\Commands\MakeModuleCommand;
use AlpDevelop\LivewirePanel\Commands\MakePageCommand;
use AlpDevelop\LivewirePanel\Commands\MakePluginCommand;
use AlpDevelop\LivewirePanel\Commands\MakeStyleCommand;
use AlpDevelop\LivewirePanel\Commands\MakeThemeCommand;
use AlpDevelop\LivewirePanel\Commands\MakeWidgetCommand;
use AlpDevelop\LivewirePanel\Commands\UpgradeCommand;
use AlpDevelop\LivewirePanel\Compat\LivewireCompat;
use AlpDevelop\LivewirePanel\Config\PanelConfig;
use AlpDevelop\LivewirePanel\Config\PanelStyleConfig;
use AlpDevelop\LivewirePanel\Http\Controllers\AssetController;
use AlpDevelop\LivewirePanel\Http\Controllers\LocaleController;
use AlpDevelop\LivewirePanel\Http\Middleware\PanelAuthMiddleware;
use AlpDevelop\LivewirePanel\Http\Middleware\SetPanelLocale;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\ForgotPasswordComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\LoginComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\RegisterComponent;
use AlpDevelop\LivewirePanel\Modules\Dashboard\Http\Livewire\DashboardPage;
use AlpDevelop\LivewirePanel\Modules\Users\Http\Livewire\UsersPage;
use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use AlpDevelop\LivewirePanel\Navigation\NavigationRegistry;
use AlpDevelop\LivewirePanel\Plugins\PluginRegistry;
use AlpDevelop\LivewirePanel\Notifications\NotificationRegistry;
use AlpDevelop\LivewirePanel\Search\SearchRegistry;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;
use AlpDevelop\LivewirePanel\View\Components\Alert;
use AlpDevelop\LivewirePanel\View\Components\Button;
use AlpDevelop\LivewirePanel\View\Components\Card;
use AlpDevelop\LivewirePanel\View\Components\Icon;
use AlpDevelop\LivewirePanel\View\Components\LocaleSelector;
use AlpDevelop\LivewirePanel\View\Components\Portal;
use AlpDevelop\LivewirePanel\View\Livewire\Navbar;
use AlpDevelop\LivewirePanel\View\Livewire\PanelNotifications;
use AlpDevelop\LivewirePanel\View\Livewire\PanelSearch;
use AlpDevelop\LivewirePanel\View\Livewire\Sidebar;
use AlpDevelop\LivewirePanel\Widgets\ChartWidget;
use AlpDevelop\LivewirePanel\Widgets\RecentTableWidget;
use AlpDevelop\LivewirePanel\Widgets\StatsCardWidget;
use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class LivewirePanelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-livewire-panel.php', 'laravel-livewire-panel');

        $this->app->singleton(ThemeRegistry::class);
        $this->app->singleton(ModuleRegistry::class);
        $this->app->singleton(PluginRegistry::class);
        $this->app->singleton(CdnPluginResolver::class);
        $this->app->singleton(CdnManager::class);
        $this->app->singleton(NavigationRegistry::class);
        $this->app->singleton(NotificationRegistry::class);
        $this->app->singleton(SearchRegistry::class);
        $this->app->singleton(WidgetRegistry::class);
        $this->app->singleton(PanelContext::class);
        $this->app->singleton(PanelAccessRegistry::class);

        $this->app->singleton(PanelConfig::class, function ($app) {
            return new PanelConfig($app['config']->get('laravel-livewire-panel', []));
        });

        $this->app->singleton(PanelStyleConfig::class, function ($app) {
            $styleConfig = new PanelStyleConfig();
            $styleConfig->loadFromDirectory(config_path('laravel-livewire-panel'));

            return $styleConfig;
        });

        $this->app->singleton(PanelGate::class, function ($app) {
            return new PanelGate($app->make(PanelResolver::class));
        });

        $this->app->singleton(PanelResolver::class, function ($app) {
            return new PanelResolver($app->make(PanelConfig::class));
        });

        $this->app->singleton(PanelKernel::class, function ($app) {
            return new PanelKernel(
                $app->make(PanelConfig::class),
                $app->make(PanelStyleConfig::class),
                $app->make(ThemeRegistry::class),
                $app->make(ModuleRegistry::class),
                $app->make(PluginRegistry::class),
                $app->make(NavigationRegistry::class),
                $app->make(SearchRegistry::class),
                $app->make(WidgetRegistry::class),
            );
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'panel');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'panel');

        $this->publishes([
            __DIR__ . '/../config/laravel-livewire-panel.php' => config_path('laravel-livewire-panel.php'),
        ], 'panel-config');

        $this->publishes([
            __DIR__ . '/../config/laravel-livewire-panel' => config_path('laravel-livewire-panel'),
        ], 'panel-styles');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/panel'),
        ], 'panel-views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/panel'),
        ], 'panel-lang');

        Blade::componentNamespace('AlpDevelop\\LivewirePanel\\View\\Components', 'panel');

        $this->loadViewComponentsAs('panel', [
            Button::class,
            Card::class,
            Alert::class,
            Icon::class,
            Portal::class,
            LocaleSelector::class,
        ]);

        $this->registerBladeDirectives();

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('panel.auth', PanelAuthMiddleware::class);

        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->appendMiddlewareToGroup('web', SetPanelLocale::class);

        $router->get('/panel-locale/{locale}', LocaleController::class)
            ->middleware(['web'])
            ->name('panel.locale.switch');

        $router->get('/_panel/assets/{file}', AssetController::class)
            ->where('file', '[a-zA-Z0-9\/_\-\.]+')
            ->name('panel.asset');

        if (class_exists(\Livewire\Livewire::class)) {
            LivewireCompat::registerComponent('panel-auth-login', LoginComponent::class);
            LivewireCompat::registerComponent('panel-auth-register', RegisterComponent::class);
            LivewireCompat::registerComponent('panel-auth-forgot-password', ForgotPasswordComponent::class);
            LivewireCompat::registerComponent('panel-sidebar', Sidebar::class);
            LivewireCompat::registerComponent('panel-navbar', Navbar::class);
            LivewireCompat::registerComponent('panel-search', PanelSearch::class);
            LivewireCompat::registerComponent('panel-notifications', PanelNotifications::class);
            LivewireCompat::registerComponent('panel-dashboard-page', DashboardPage::class);
            LivewireCompat::registerComponent('panel-users-page', UsersPage::class);
            LivewireCompat::registerComponent('widgets.stats-card', StatsCardWidget::class);
            LivewireCompat::registerComponent('widgets.chart-widget', ChartWidget::class);
            LivewireCompat::registerComponent('widgets.recent-table', RecentTableWidget::class);
        }

        $widgetRegistry = $this->app->make(WidgetRegistry::class);
        $widgetRegistry->register('stats-card', StatsCardWidget::class);
        $widgetRegistry->register('chart-widget', ChartWidget::class);
        $widgetRegistry->register('recent-table', RecentTableWidget::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                MakeWidgetCommand::class,
                MakeModuleCommand::class,
                MakePageCommand::class,
                MakePluginCommand::class,
                MakeStyleCommand::class,
                MakeThemeCommand::class,
                MakeComponentCommand::class,
                UpgradeCommand::class,
            ]);
        }

        $kernel = $this->app->make(PanelKernel::class);
        $kernel->boot();
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('panelCssVars', fn () => '<?php echo \AlpDevelop\LivewirePanel\PanelRenderer::cssVars(); ?>');

        Blade::directive('panelCssAssets', fn ($expression) => "<?php echo \\AlpDevelop\\LivewirePanel\\PanelRenderer::cssAssets({$expression}); ?>");

        Blade::directive('panelJsAssets', fn ($expression) => "<?php echo \\AlpDevelop\\LivewirePanel\\PanelRenderer::jsAssets({$expression}); ?>");

        Blade::directive('panelLayoutConfig', fn () => '<?php $__panelLayout = \AlpDevelop\LivewirePanel\PanelRenderer::layoutConfig(); ?>');

        Blade::directive('panelHtmlAttributes', fn () => '<?php echo \AlpDevelop\LivewirePanel\PanelRenderer::htmlAttributes(); ?>');

        Blade::directive('panelCdnLibraries', function (string $expression) {
            return <<<'PHP'
<?php
$__panelId     = \AlpDevelop\LivewirePanel\PanelRenderer::resolvePanelId();
$__panelConfig = app(\AlpDevelop\LivewirePanel\PanelKernel::class)->config()->get($__panelId);
$__cdnManager  = app(\AlpDevelop\LivewirePanel\Cdn\CdnManager::class);
echo $__cdnManager->renderCssLinks($__panelConfig, request()->path());
echo $__cdnManager->renderJsScripts($__panelConfig, request()->path());
?>
PHP;
        });
    }
}
