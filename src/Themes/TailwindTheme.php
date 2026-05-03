<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Themes;

final class TailwindTheme extends AbstractTheme
{
    public function id(): string
    {
        return 'tailwind';
    }

    /** @return list<string> */
    public function cssAssets(): array
    {
        return [];
    }

    /** @return list<string> */
    public function jsAssets(): array
    {
        return [];
    }

    /** @param array<string, mixed> $styleConfig */
    public function headHtml(array $styleConfig = []): string
    {
        $c = $this->resolveThemeColors($styleConfig);

        $html = '<script src="https://cdn.tailwindcss.com"></script>' . "\n" .
               '<script>' . "\n" .
               'tailwind.config = {' . "\n" .
               '  prefix: "tw-",' . "\n" .
               '  corePlugins: { preflight: false },' . "\n" .
               '  theme: {' . "\n" .
               '    extend: {' . "\n" .
               '      colors: {' . "\n" .
               '        primary:   "' . $c['primary'] . '",' . "\n" .
               '        secondary: "' . $c['secondary'] . '",' . "\n" .
               '        success:   "' . $c['success'] . '",' . "\n" .
               '        danger:    "' . $c['danger'] . '",' . "\n" .
               '        warning:   "' . $c['warning'] . '",' . "\n" .
               '        info:      "' . $c['info'] . '",' . "\n" .
               '      },' . "\n" .
               '      borderRadius: { DEFAULT: "' . $c['radius'] . '" },' . "\n" .
               '      fontFamily: { sans: ["' . explode(',', $c['font'])[0] . '", "sans-serif"] },' . "\n" .
               '    },' . "\n" .
               '  },' . "\n" .
               '};' . "\n" .
               '</script>';

        if (!empty($styleConfig['layout']['dark_mode'])) {
            $html .= "\n" . '<style>'
                . 'html[data-theme="dark"] .tw-bg-white{background-color:var(--panel-card-bg)!important}'
                . 'html[data-theme="dark"] .tw-bg-gray-50{background-color:var(--panel-content-bg)!important}'
                . 'html[data-theme="dark"] .tw-bg-gray-100{background-color:var(--panel-content-bg)!important}'
                . 'html[data-theme="dark"] .tw-bg-gray-200{background-color:var(--panel-card-bg)!important}'
                . 'html[data-theme="dark"] .tw-text-gray-500{color:var(--panel-text-muted)!important}'
                . 'html[data-theme="dark"] .tw-text-gray-600{color:var(--panel-text-muted)!important}'
                . 'html[data-theme="dark"] .tw-text-gray-700{color:var(--panel-text-primary)!important}'
                . 'html[data-theme="dark"] .tw-text-gray-800{color:var(--panel-text-primary)!important}'
                . 'html[data-theme="dark"] .tw-text-gray-900{color:var(--panel-text-primary)!important}'
                . 'html[data-theme="dark"] .tw-border-gray-200{border-color:var(--panel-card-border)!important}'
                . 'html[data-theme="dark"] .tw-border-gray-300{border-color:var(--panel-card-border)!important}'
                . 'html[data-theme="dark"] .tw-bg-blue-50,html[data-theme="dark"] .tw-bg-green-50,html[data-theme="dark"] .tw-bg-red-50,html[data-theme="dark"] .tw-bg-yellow-50,html[data-theme="dark"] .tw-bg-cyan-50{background-color:rgba(255,255,255,0.05)!important}'
                . 'html[data-theme="dark"] .tw-text-blue-800,html[data-theme="dark"] .tw-text-green-800,html[data-theme="dark"] .tw-text-red-800,html[data-theme="dark"] .tw-text-yellow-800,html[data-theme="dark"] .tw-text-cyan-800{opacity:0.9}'
                . 'html[data-theme="dark"] .tw-border-blue-200,html[data-theme="dark"] .tw-border-green-200,html[data-theme="dark"] .tw-border-red-200,html[data-theme="dark"] .tw-border-yellow-200,html[data-theme="dark"] .tw-border-cyan-200{border-color:var(--panel-card-border)!important}'
                . '</style>';
        }

        return $html;
    }

    /** @return array<string, array<string, string>> */
    public function componentClasses(): array
    {
        return [
            'button' => [
                'base'      => 'tw-inline-flex tw-items-center tw-font-medium tw-rounded tw-transition-colors tw-cursor-pointer',
                'primary'   => 'tw-bg-primary hover:tw-opacity-90 tw-text-white',
                'secondary' => 'tw-bg-secondary hover:tw-opacity-90 tw-text-white',
                'success'   => 'tw-bg-success hover:tw-opacity-90 tw-text-white',
                'danger'    => 'tw-bg-danger hover:tw-opacity-90 tw-text-white',
                'warning'   => 'tw-bg-warning hover:tw-opacity-90 tw-text-gray-900',
                'info'      => 'tw-bg-info hover:tw-opacity-90 tw-text-white',
                'light'     => 'tw-bg-gray-100 hover:tw-bg-gray-200 tw-text-gray-800',
                'dark'      => 'tw-bg-gray-800 hover:tw-bg-gray-900 tw-text-white',
                'sm'        => 'tw-px-3 tw-py-1.5 tw-text-sm',
                'md'        => 'tw-px-4 tw-py-2 tw-text-sm',
                'lg'        => 'tw-px-6 tw-py-3 tw-text-lg',
            ],

            'alert' => [
                'base'        => 'tw-flex tw-items-start tw-gap-3 tw-rounded-lg tw-border tw-p-4',
                'primary'     => 'tw-bg-blue-50 tw-border-blue-200 tw-text-blue-800',
                'secondary'   => 'tw-bg-gray-50 tw-border-gray-200 tw-text-gray-800',
                'success'     => 'tw-bg-green-50 tw-border-green-200 tw-text-green-800',
                'danger'      => 'tw-bg-red-50 tw-border-red-200 tw-text-red-800',
                'warning'     => 'tw-bg-yellow-50 tw-border-yellow-200 tw-text-yellow-800',
                'info'        => 'tw-bg-cyan-50 tw-border-cyan-200 tw-text-cyan-800',
                'close'       => 'tw-ml-auto tw-text-current tw-opacity-60 hover:tw-opacity-100',
                'close_tag'   => 'button',
                'close_attrs' => 'onclick="this.parentElement.remove()"',
            ],
            'card' => [
                'root'   => 'tw-bg-white tw-rounded-lg tw-border tw-border-gray-200 tw-shadow-sm',
                'header' => 'tw-px-4 tw-py-3 tw-border-b tw-border-gray-200 tw-font-semibold tw-text-gray-700',
                'body'   => 'tw-p-4',
                'footer' => 'tw-px-4 tw-py-3 tw-border-t tw-border-gray-200 tw-text-gray-500 tw-text-sm',
            ],

        ];
    }
}
