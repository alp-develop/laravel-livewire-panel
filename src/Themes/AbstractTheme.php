<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Themes;

/**
 * Base implementation for panel themes.
 *
 * Extend this class and implement all abstract methods from `ThemeInterface`
 * to create a custom theme. Use the protected helpers `resolveThemeColors()`,
 * `resolveDarkColors()`, and `sanitizeCssValue()` when building CSS output.
 *
 * @see ThemeInterface
 */
abstract class AbstractTheme implements ThemeInterface
{
    /** @param array<string, mixed> $styleConfig */
    public function headHtml(array $styleConfig = []): string
    {
        return '';
    }

    protected function sanitizeCssValue(string $value): string
    {
        $value = preg_replace('/[;{}\\\\<>"\']/', '', $value) ?? '';
        $value = preg_replace('/expression\s*\(/i', '', $value) ?? '';
        $value = preg_replace('/javascript\s*:/i', '', $value) ?? '';

        return preg_replace('/\\\\[0-9a-fA-F]{1,6}\s?/', '', $value) ?? '';
    }

    /** @param array<string, mixed> $styleConfig
     *  @return array{primary: string, secondary: string, success: string, danger: string, warning: string, info: string, font: string, radius: string} */
    protected function resolveThemeColors(array $styleConfig): array
    {
        $theming = $styleConfig['theming'] ?? [];
        $pLight  = ($theming['panel'] ?? [])['light'] ?? [];

        return [
            'primary'   => $this->sanitizeCssValue($pLight['primary'] ?? $theming['primary'] ?? '#4f46e5'),
            'secondary' => $this->sanitizeCssValue($theming['secondary']     ?? '#6c757d'),
            'success'   => $this->sanitizeCssValue($theming['success']       ?? '#198754'),
            'danger'    => $this->sanitizeCssValue($theming['danger']        ?? '#dc3545'),
            'warning'   => $this->sanitizeCssValue($theming['warning']       ?? '#ffc107'),
            'info'      => $this->sanitizeCssValue($theming['info']          ?? '#0dcaf0'),
            'font'      => $this->sanitizeCssValue($theming['font_family']   ?? 'sans-serif'),
            'radius'    => $this->sanitizeCssValue($theming['border_radius'] ?? '8px'),
        ];
    }

    /** @param array<string, mixed> $styleConfig
     *  @return array{primary: string, surface: string, background: string, border: string, text: string, text_muted: string} */
    protected function resolveDarkColors(array $styleConfig): array
    {
        $theming = $styleConfig['theming'] ?? [];
        $pDark   = ($theming['panel'] ?? [])['dark'] ?? [];

        return [
            'primary'    => $this->sanitizeCssValue($pDark['primary']    ?? $theming['primary'] ?? '#818cf8'),
            'surface'    => $this->sanitizeCssValue($pDark['surface']    ?? '#1e293b'),
            'background' => $this->sanitizeCssValue($pDark['background'] ?? '#0f172a'),
            'border'     => $this->sanitizeCssValue($pDark['border']     ?? '#334155'),
            'text'       => $this->sanitizeCssValue($pDark['text']       ?? '#e2e8f0'),
            'text_muted' => $this->sanitizeCssValue($pDark['text_muted'] ?? '#94a3b8'),
        ];
    }

    /** @param array<string, mixed> $styleConfig */
    public function cssVariables(array $styleConfig): string
    {
        $theming  = $styleConfig['theming'] ?? [];
        $tSidebar = $theming['sidebar'] ?? [];
        $tNavbar  = $theming['navbar']  ?? [];
        $tPanel   = $theming['panel']   ?? [];
        $tAuth    = $theming['auth']    ?? [];
        $sLight   = $tSidebar['light']  ?? [];
        $nLight   = $tNavbar['light']   ?? [];
        $pLight   = $tPanel['light']    ?? [];
        $aLight   = $tAuth['light']     ?? [];

        $primary = $this->sanitizeCssValue($pLight['primary'] ?? $theming['primary'] ?? '#4f46e5');

        $vars = [
            '--panel-primary:             ' . $primary,
            '--panel-secondary:           ' . $this->sanitizeCssValue($theming['secondary']     ?? '#6c757d'),
            '--panel-success:             ' . $this->sanitizeCssValue($theming['success']       ?? '#198754'),
            '--panel-danger:              ' . $this->sanitizeCssValue($theming['danger']        ?? '#dc3545'),
            '--panel-warning:             ' . $this->sanitizeCssValue($theming['warning']       ?? '#ffc107'),
            '--panel-info:                ' . $this->sanitizeCssValue($theming['info']          ?? '#0dcaf0'),
            '--panel-font:                ' . $this->sanitizeCssValue($theming['font_family']   ?? 'sans-serif'),
            '--panel-font-size:           ' . $this->sanitizeCssValue($theming['font_size']     ?? '14px'),
            '--panel-radius:              ' . $this->sanitizeCssValue($theming['border_radius'] ?? '8px'),
            '--panel-sidebar-width:       ' . $this->sanitizeCssValue($tSidebar['width']           ?? '260px'),
            '--panel-sidebar-collapsed:   ' . $this->sanitizeCssValue($tSidebar['collapsed_width'] ?? '64px'),
            '--panel-sidebar-item-size:   ' . $this->sanitizeCssValue($tSidebar['item_font_size']  ?? '0.9rem'),
            '--panel-sidebar-item-weight: ' . $this->sanitizeCssValue($tSidebar['item_font_weight'] ?? '600'),
            '--panel-sidebar-bg:          ' . $this->sanitizeCssValue($sLight['background']  ?? '#1e293b'),
            '--panel-sidebar-text:        ' . $this->sanitizeCssValue($sLight['text']        ?? '#cbd5e1'),
            '--panel-sidebar-muted:       ' . $this->sanitizeCssValue($sLight['muted']       ?? '#64748b'),
            '--panel-sidebar-active-bg:   ' . $this->sanitizeCssValue($sLight['active_bg']   ?? $primary),
            '--panel-sidebar-active-text: ' . $this->sanitizeCssValue($sLight['active_text'] ?? '#ffffff'),
            '--panel-navbar-height:       ' . $this->sanitizeCssValue($tNavbar['height']     ?? '60px'),
            '--panel-navbar-bg:           ' . $this->sanitizeCssValue($nLight['background']  ?? '#ffffff'),
            '--panel-navbar-text:         ' . $this->sanitizeCssValue($nLight['text']        ?? '#1e293b'),
            '--panel-navbar-border:       ' . $this->sanitizeCssValue($nLight['border']      ?? '#e2e8f0'),
            '--panel-navbar-icons:        ' . $this->sanitizeCssValue($nLight['icons_color'] ?? '#64748b'),
            '--panel-navbar-icons-hover:  ' . $this->sanitizeCssValue($nLight['icons_hover_color'] ?? '#334155'),
            '--panel-content-bg:          ' . $this->sanitizeCssValue($pLight['background']  ?? '#f4f6f9'),
            '--panel-card-bg:             ' . $this->sanitizeCssValue($pLight['surface']     ?? '#ffffff'),
            '--panel-card-border:         ' . $this->sanitizeCssValue($pLight['border']      ?? '#e2e8f0'),
            '--panel-text-primary:        ' . $this->sanitizeCssValue($pLight['text']        ?? '#333333'),
            '--panel-text-muted:          ' . $this->sanitizeCssValue($pLight['text_muted']  ?? '#6c757d'),
            '--panel-auth-bg:             ' . $this->sanitizeCssValue($aLight['background']  ?? '#f4f6f9'),
        ];

        $layout = $styleConfig['layout'] ?? [];
        if (!empty($layout['content_max_width'])) {
            $vars[] = '--panel-content-max-width: ' . $this->sanitizeCssValue($layout['content_max_width']);
        }

        return '    ' . implode(";\n    ", $vars) . ';';
    }

    /** @param array<string, mixed> $styleConfig */
    public function darkCssVariables(array $styleConfig): string
    {
        $theming  = $styleConfig['theming'] ?? [];
        $tSidebar = $theming['sidebar'] ?? [];
        $tNavbar  = $theming['navbar']  ?? [];
        $tPanel   = $theming['panel']   ?? [];
        $tAuth    = $theming['auth']    ?? [];
        $sDark    = $tSidebar['dark']   ?? [];
        $nDark    = $tNavbar['dark']    ?? [];
        $pDark    = $tPanel['dark']     ?? [];
        $aDark    = $tAuth['dark']      ?? [];

        $darkPrimary = $this->sanitizeCssValue($pDark['primary'] ?? $theming['primary'] ?? '#818cf8');

        $vars = [
            '--panel-primary:             ' . $darkPrimary,
            '--panel-sidebar-bg:          ' . $this->sanitizeCssValue($sDark['background']  ?? '#0f172a'),
            '--panel-sidebar-text:        ' . $this->sanitizeCssValue($sDark['text']        ?? '#94a3b8'),
            '--panel-sidebar-muted:       ' . $this->sanitizeCssValue($sDark['muted']       ?? '#475569'),
            '--panel-sidebar-active-bg:   ' . $this->sanitizeCssValue($sDark['active_bg']   ?? $darkPrimary),
            '--panel-sidebar-active-text: ' . $this->sanitizeCssValue($sDark['active_text'] ?? '#ffffff'),
            '--panel-navbar-bg:           ' . $this->sanitizeCssValue($nDark['background']  ?? '#1e293b'),
            '--panel-navbar-text:         ' . $this->sanitizeCssValue($nDark['text']        ?? '#e2e8f0'),
            '--panel-navbar-border:       ' . $this->sanitizeCssValue($nDark['border']      ?? '#334155'),
            '--panel-navbar-icons:        ' . $this->sanitizeCssValue($nDark['icons_color'] ?? '#94a3b8'),
            '--panel-navbar-icons-hover:  ' . $this->sanitizeCssValue($nDark['icons_hover_color'] ?? '#e2e8f0'),
            '--panel-content-bg:          ' . $this->sanitizeCssValue($pDark['background']  ?? '#0f172a'),
            '--panel-card-bg:             ' . $this->sanitizeCssValue($pDark['surface']     ?? '#1e293b'),
            '--panel-card-border:         ' . $this->sanitizeCssValue($pDark['border']      ?? '#334155'),
            '--panel-text-primary:        ' . $this->sanitizeCssValue($pDark['text']        ?? '#e2e8f0'),
            '--panel-text-muted:          ' . $this->sanitizeCssValue($pDark['text_muted']  ?? '#94a3b8'),
            '--panel-auth-bg:             ' . $this->sanitizeCssValue($aDark['background']  ?? '#0f172a'),
        ];

        return '    ' . implode(";\n    ", $vars) . ';';
    }

    public function classes(string $component, string $slot = 'root'): string
    {
        return $this->componentClasses()[$component][$slot] ?? '';
    }
}
