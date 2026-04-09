<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel;

use AlpDevelop\LivewirePanel\Cdn\CdnPluginResolver;

final class PanelRenderer
{
    /** @var array<string, array<string, mixed>> */
    private static array $cache = [];

    public static function assetUrl(string $file): string
    {
        return url('/_panel/assets/' . ltrim($file, '/'));
    }

    public static function resolvePanelId(): string
    {
        $context = app(PanelContext::class);

        if ($context->resolved()) {
            return $context->get();
        }

        $resolver = app(PanelResolver::class);
        return $resolver->resolveFromRequest(request());
    }

    public static function cssVars(): string
    {
        $panelId = self::resolvePanelId();

        if (isset(self::$cache['cssVars'][$panelId])) {
            return self::$cache['cssVars'][$panelId];
        }

        $kernel   = app(PanelKernel::class);
        $panelCfg = $kernel->config()->get($panelId);
        $styleId  = $panelCfg['customization'] ?? 'default';
        $styleCfg = $kernel->styleConfig()->has($styleId) ? $kernel->styleConfig()->get($styleId) : [];
        $theme    = $kernel->themes()->resolve($panelCfg['theme'] ?? 'bootstrap5');
        $vars     = $theme->cssVariables($styleCfg);

        $output = "<style>:root {\n{$vars}\n}";

        if ($styleCfg['layout']['dark_mode'] ?? false) {
            $darkVars = $theme->darkCssVariables($styleCfg);
            $output .= "\nhtml[data-theme=\"dark\"] {\n{$darkVars}\n}";
        }

        $output .= '</style>';

        self::$cache['cssVars'][$panelId] = $output;

        return $output;
    }

    public static function cssAssets(string $layout = ''): string
    {
        $kernel    = app(PanelKernel::class);
        $resolver  = app(CdnPluginResolver::class);
        $panelId   = self::resolvePanelId();
        $panelCfg  = $kernel->config()->get($panelId);
        $styleId   = $panelCfg['customization'] ?? 'default';
        $styleCfg  = $kernel->styleConfig()->has($styleId) ? $kernel->styleConfig()->get($styleId) : [];
        $theme     = $kernel->themes()->resolve($panelCfg['theme'] ?? 'bootstrap5');
        $cdnAssets = $resolver->resolveAssets($panelCfg, request()->path());

        $output = '';

        $favicon = $styleCfg['layout']['favicon'] ?? null;
        if ($favicon) {
            $output .= '<link rel="icon" href="' . e($favicon) . '">' . "\n";
        }

        $output .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">' . "\n";

        foreach ($theme->cssAssets() as $url) {
            $output .= '<link rel="stylesheet" href="' . e($url) . '">' . "\n";
        }

        foreach ($cdnAssets['css'] as $url) {
            $output .= '<link rel="stylesheet" href="' . e($url) . '">' . "\n";
        }

        $output .= $theme->headHtml($styleCfg);

        $output .= '<link rel="stylesheet" href="' . e(self::assetUrl('css/panel-base.css')) . '">' . "\n";

        if (in_array($layout, ['app', 'auth', 'public'], true)) {
            $output .= '<link rel="stylesheet" href="' . e(self::assetUrl('css/panel-' . $layout . '.css')) . '">' . "\n";
        }

        $output .= '<script src="' . e(self::assetUrl('js/panel-init.js')) . '"></script>' . "\n";

        return $output;
    }

    public static function jsAssets(string $layout = ''): string
    {
        $kernel    = app(PanelKernel::class);
        $resolver  = app(CdnPluginResolver::class);
        $panelId   = self::resolvePanelId();
        $panelCfg  = $kernel->config()->get($panelId);
        $theme     = $kernel->themes()->resolve($panelCfg['theme'] ?? 'bootstrap5');
        $cdnAssets = $resolver->resolveAssets($panelCfg, request()->path());

        $output = '';

        foreach ($theme->jsAssets() as $url) {
            $output .= '<script src="' . e($url) . '" data-navigate-once></script>' . "\n";
        }

        foreach ($cdnAssets['js'] as $url) {
            $output .= '<script src="' . e($url) . '" data-navigate-once></script>' . "\n";
        }

        if ($layout === 'app') {
            $output .= '<script src="' . e(self::assetUrl('js/panel-app.js')) . '" data-navigate-once></script>' . "\n";
        }

        return $output;
    }

    public static function layoutConfig(): array
    {
        $panelId = self::resolvePanelId();

        if (isset(self::$cache['layoutConfig'][$panelId])) {
            return self::$cache['layoutConfig'][$panelId];
        }

        $kernel   = app(PanelKernel::class);
        $panelCfg = $kernel->config()->get($panelId);
        $styleId  = $panelCfg['customization'] ?? 'default';
        $styleCfg = $kernel->styleConfig()->has($styleId) ? $kernel->styleConfig()->get($styleId) : [];

        $result = [
            'favicon'                => $styleCfg['layout']['favicon'] ?? null,
            'dark_mode'              => $styleCfg['layout']['dark_mode'] ?? false,
            'dark_mode_show_on_auth' => $styleCfg['layout']['dark_mode_show_on_auth'] ?? false,
            'dark_mode_classes'      => $styleCfg['layout']['dark_mode_classes'] ?? [],
            'dark_mode_dispatch'     => $styleCfg['layout']['dark_mode_dispatch'] ?? null,
            'dark_mode_callback'     => $styleCfg['layout']['dark_mode_callback'] ?? null,
            'page_transition'    => $styleCfg['layout']['page_transition'] ?? null,
            'back_to_top'        => $styleCfg['layout']['back_to_top'] ?? false,
            'content_max_width'  => $styleCfg['layout']['content_max_width'] ?? null,
            'show_search'        => $styleCfg['navbar']['show_search'] ?? true,
            'show_notifications'             => $styleCfg['navbar']['show_notifications'] ?? true,
            'notification_polling'           => $styleCfg['navbar']['notification_polling'] ?? true,
            'notification_polling_interval'  => $styleCfg['navbar']['notification_polling_interval'] ?? 30,
            'show_user_menu'     => $styleCfg['navbar']['show_user_menu'] ?? true,
            'navbar_show_avatar' => $styleCfg['navbar']['show_avatar'] ?? true,
            'show_breadcrumbs'   => $styleCfg['navbar']['show_breadcrumbs'] ?? true,
            'navbar_sticky'      => $styleCfg['navbar']['sticky'] ?? true,
            'show_page_title'    => $styleCfg['navbar']['show_page_title'] ?? true,
            'sidebar_initial_state'      => $styleCfg['sidebar']['initial_state'] ?? 'expanded',
            'sidebar_collapsible'        => $styleCfg['sidebar']['collapsible'] ?? true,
            'sidebar_persist_state'      => $styleCfg['sidebar']['persist_state'] ?? true,
            'sidebar_icons_only_when_collapsed' => $styleCfg['sidebar']['icons_only_when_collapsed'] ?? true,
            'sidebar_overlay_on_mobile'  => $styleCfg['sidebar']['overlay_on_mobile'] ?? true,
            'sidebar_logo'       => $styleCfg['sidebar']['logo'] ?? null,
            'sidebar_logo_height' => $styleCfg['sidebar']['logo_height'] ?? '40px',
            'sidebar_logo_width'  => $styleCfg['sidebar']['logo_width'] ?? 'auto',
            'sidebar_logo_class'  => $styleCfg['sidebar']['logo_class'] ?? '',
            'sidebar_header_text' => $styleCfg['sidebar']['header_text'] ?? 'Panel Admin',
            'sidebar_header_text_wrap' => $styleCfg['sidebar']['header_text_wrap'] ?? true,
            'sidebar_show_user_menu' => $styleCfg['sidebar']['show_user_menu'] ?? false,
            'sidebar_show_avatar'    => $styleCfg['sidebar']['show_avatar'] ?? true,
            'avatar_resolver'        => $styleCfg['layout']['avatar_resolver'] ?? null,
        ];

        self::$cache['layoutConfig'][$panelId] = $result;

        return $result;
    }

    public static function htmlAttributes(): string
    {
        $layout = self::layoutConfig();

        $attrs = [
            'data-navbar-sticky'    => $layout['navbar_sticky'] ? 'true' : 'false',
            'data-sidebar-text-wrap' => $layout['sidebar_header_text_wrap'] ? 'true' : 'false',
            'data-sidebar-icons-only' => ($layout['sidebar_icons_only_when_collapsed'] ?? true) ? 'true' : 'false',
            'data-sidebar-initial'  => $layout['sidebar_initial_state'] ?? 'expanded',
            'data-sidebar-persist'  => ($layout['sidebar_persist_state'] ?? true) ? 'true' : 'false',
            'data-sidebar-collapsible' => ($layout['sidebar_collapsible'] ?? true) ? 'true' : 'false',
            'data-page-transition'  => $layout['page_transition'] ?? '',
            'data-dark-classes'     => implode(' ', $layout['dark_mode_classes'] ?? []),
            'data-dark-dispatch'    => $layout['dark_mode_dispatch'] ?? '',
            'data-dark-callback'    => $layout['dark_mode_callback'] ?? '',
        ];

        $parts = [];

        foreach ($attrs as $key => $value) {
            $parts[] = e($key) . '="' . e((string) $value) . '"';
        }

        return implode(' ', $parts);
    }
}
