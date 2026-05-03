<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Facades;

use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelKernel;
use AlpDevelop\LivewirePanel\PanelPortalBuilder;
use AlpDevelop\LivewirePanel\PanelRenderer;
use AlpDevelop\LivewirePanel\PanelResolver;
use AlpDevelop\LivewirePanel\Search\SearchRegistryInterface;
use AlpDevelop\LivewirePanel\Notifications\NotificationRegistryInterface;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;
use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use AlpDevelop\LivewirePanel\Plugins\PluginRegistry;
use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string currentId()
 * @method static PanelPortalBuilder for(string $panelId)
 * @method static string route(string $routeName, array<string, mixed> $parameters = [])
 * @method static PanelKernel kernel()
 * @method static ThemeRegistry themes()
 * @method static ModuleRegistry modules()
 * @method static PluginRegistry plugins()
 * @method static WidgetRegistry widgets()
 * @method static SearchRegistryInterface search()
 * @method static NotificationRegistryInterface notifications()
 * @method static void clearCaches()
 *
 * @see \AlpDevelop\LivewirePanel\PanelManager
 */
final class Panel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'panel';
    }
}
