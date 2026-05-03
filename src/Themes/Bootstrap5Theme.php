<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Themes;

final class Bootstrap5Theme extends AbstractTheme
{
    public function id(): string
    {
        return 'bootstrap5';
    }

    /** @return list<string> */
    public function cssAssets(): array
    {
        return [
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        ];
    }

    /** @return list<string> */
    public function jsAssets(): array
    {
        return [
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        ];
    }

    /** @param array<string, mixed> $styleConfig */
    public function cssVariables(array $styleConfig): string
    {
        $base = parent::cssVariables($styleConfig);
        $c    = $this->resolveThemeColors($styleConfig);

        $bs = [
            '--bs-primary:           ' . $c['primary'],
            '--bs-primary-rgb:       ' . $this->hexToRgb($c['primary']),
            '--bs-secondary:         ' . $c['secondary'],
            '--bs-secondary-rgb:     ' . $this->hexToRgb($c['secondary']),
            '--bs-success:           ' . $c['success'],
            '--bs-success-rgb:       ' . $this->hexToRgb($c['success']),
            '--bs-danger:            ' . $c['danger'],
            '--bs-danger-rgb:        ' . $this->hexToRgb($c['danger']),
            '--bs-warning:           ' . $c['warning'],
            '--bs-warning-rgb:       ' . $this->hexToRgb($c['warning']),
            '--bs-info:              ' . $c['info'],
            '--bs-info-rgb:          ' . $this->hexToRgb($c['info']),
            '--bs-body-font-family:  ' . $c['font'],
            '--bs-body-font-size:    ' . $this->sanitizeCssValue(($styleConfig['theming'] ?? [])['font_size'] ?? '14px'),
            '--bs-border-radius:     ' . $c['radius'],
            '--bs-border-radius-sm:  calc(' . $c['radius'] . ' * 0.75)',
            '--bs-border-radius-lg:  calc(' . $c['radius'] . ' * 1.5)',
        ];

        return $base . "\n    " . implode(";\n    ", $bs) . ';';
    }

    /** @param array<string, mixed> $styleConfig */
    public function headHtml(array $styleConfig = []): string
    {
        $c = $this->resolveThemeColors($styleConfig);

        $btnCss = '';
        foreach ([
            'primary'   => $c['primary'],
            'secondary' => $c['secondary'],
            'success'   => $c['success'],
            'danger'    => $c['danger'],
            'warning'   => $c['warning'],
            'info'      => $c['info'],
        ] as $name => $color) {
            $rgb    = $this->hexToRgb($color);
            $btnCss .= '.btn-' . $name . '{'
                . '--bs-btn-bg:' . $color . ';'
                . '--bs-btn-border-color:' . $color . ';'
                . '--bs-btn-hover-bg:' . $color . ';'
                . '--bs-btn-hover-border-color:' . $color . ';'
                . '--bs-btn-active-bg:' . $color . ';'
                . '--bs-btn-active-border-color:' . $color . ';'
                . '--bs-btn-focus-shadow-rgb:' . $rgb . ';'
                . '--bs-btn-disabled-bg:' . $color . ';'
                . '--bs-btn-disabled-border-color:' . $color
                . '}'
                . '.btn-' . $name . ':hover,.btn-' . $name . ':active,.btn-' . $name . '.active{filter:brightness(0.9)}'
                . '.badge.bg-' . $name . '{background-color:' . $color . '!important}';
        }

        $html = '<style>' . $btnCss;

        $rgb = $this->hexToRgb($c['primary']);
        $encodedPrimary = str_replace('#', '%23', $c['primary']);
        $html .= '.form-control:focus{border-color:' . $c['primary'] . '!important;box-shadow:0 0 0 .25rem rgba(' . $rgb . ',.25)!important}'
            . '.form-select:focus{border-color:' . $c['primary'] . '!important;box-shadow:0 0 0 .25rem rgba(' . $rgb . ',.25)!important}'
            . '.form-check-input:focus{border-color:' . $c['primary'] . '!important;box-shadow:0 0 0 .25rem rgba(' . $rgb . ',.25)!important}'
            . '.form-check-input:checked{background-color:' . $c['primary'] . '!important;border-color:' . $c['primary'] . '!important}'
            . '.form-switch .form-check-input:checked{background-color:' . $c['primary'] . '!important;border-color:' . $c['primary'] . '!important}'
            . '.form-range::-webkit-slider-thumb{background-color:' . $c['primary'] . '}'
            . '.form-range::-moz-range-thumb{background-color:' . $c['primary'] . '}'
            . '.form-select{background-image:url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\'%3e%3cpath fill=\'none\' stroke=\'' . $encodedPrimary . '\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'m2 5 6 6 6-6\'/%3e%3c/svg%3e")!important}'
            . '.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable{background-color:' . $c['primary'] . '!important}'
            . '.select2-container--default .select2-results__option--selected{background-color:rgba(' . $rgb . ',.15)!important}'
            . '.select2-container--default.select2-container--focus .select2-selection--single,.select2-container--default.select2-container--focus .select2-selection--multiple{border-color:' . $c['primary'] . '!important}'
            . '.select2-container--default.select2-container--open .select2-selection--single,.select2-container--default.select2-container--open .select2-selection--multiple{border-color:' . $c['primary'] . '!important}'
            . '.select2-container--default .select2-selection--multiple .select2-selection__choice{background-color:' . $c['primary'] . '!important;border-color:' . $c['primary'] . '!important;color:#fff}'
            . '.flatpickr-day.selected,.flatpickr-day.startRange,.flatpickr-day.endRange,.flatpickr-day.selected.inRange{background:' . $c['primary'] . '!important;border-color:' . $c['primary'] . '!important}'
            . '.flatpickr-day.selected:hover,.flatpickr-day.startRange:hover,.flatpickr-day.endRange:hover{background:' . $c['primary'] . '!important;border-color:' . $c['primary'] . '!important;filter:brightness(0.9)}'
            . '.flatpickr-day.today{border-color:' . $c['primary'] . '!important}'
            . '.flatpickr-day.today:hover{background:' . $c['primary'] . '!important;border-color:' . $c['primary'] . '!important;color:#fff}'
            . 'input,select,textarea,input[type="date"],input[type="time"],input[type="datetime-local"],input[type="month"],input[type="week"]{accent-color:' . $c['primary'] . '!important;caret-color:' . $c['primary'] . '}'
            . ':root{--bs-primary:' . $c['primary'] . ';--bs-primary-rgb:' . $rgb . ';--bs-link-color:' . $c['primary'] . ';--bs-link-color-rgb:' . $rgb . ';--bs-link-hover-color:' . $c['primary'] . ';--bs-link-hover-color-rgb:' . $rgb . ';--bs-focus-ring-color:rgba(' . $rgb . ',.25);accent-color:' . $c['primary'] . ';caret-color:' . $c['primary'] . '}'
            . '</style>';

        if (empty($styleConfig['layout']['dark_mode'])) {
            return $html;
        }

        $d = $this->resolveDarkColors($styleConfig);

        return $html . '<style>'
            . '[data-bs-theme=dark]{'
            . '--bs-body-bg:' . $d['background'] . ';'
            . '--bs-body-color:' . $d['text'] . ';'
            . '--bs-border-color:' . $d['border'] . ';'
            . '--bs-card-bg:' . $d['surface'] . ';'
            . '--bs-card-border-color:' . $d['border'] . ';'
            . '--bs-card-cap-bg:' . $d['surface'] . ';'
            . '--bs-tertiary-bg:' . $d['background'] . ';'
            . '--bs-secondary-bg:' . $d['surface'] . ';'
            . '}'
            . 'html[data-theme="dark"] .form-control{background-color:' . $d['surface'] . ';border-color:' . $d['border'] . ';color:' . $d['text'] . '}'
            . 'html[data-theme="dark"] .form-control:focus{background-color:' . $d['surface'] . ';border-color:var(--panel-primary);color:' . $d['text'] . '}'
            . 'html[data-theme="dark"] .form-select{background-color:' . $d['surface'] . ';border-color:' . $d['border'] . ';color:' . $d['text'] . '}'
            . 'html[data-theme="dark"] .input-group-text{background-color:' . $d['background'] . ';border-color:' . $d['border'] . ';color:' . $d['text_muted'] . '}'
            . '</style>';
    }

    /** @return array<string, array<string, string>> */
    public function componentClasses(): array
    {
        return [
            'button' => [
                'base'      => 'btn',
                'primary'   => 'btn-primary',
                'secondary' => 'btn-secondary',
                'success'   => 'btn-success',
                'danger'    => 'btn-danger',
                'warning'   => 'btn-warning',
                'info'      => 'btn-info',
                'light'     => 'btn-light',
                'dark'      => 'btn-dark',
                'sm'        => 'btn-sm',
                'lg'        => 'btn-lg',
            ],

            'alert' => [
                'base'        => 'alert',
                'primary'     => 'alert-primary',
                'secondary'   => 'alert-secondary',
                'success'     => 'alert-success',
                'danger'      => 'alert-danger',
                'warning'     => 'alert-warning',
                'info'        => 'alert-info',
                'dismissible' => '',
                'close'       => '',
                'close_attrs' => 'onclick="this.closest(\'.alert\').remove()" aria-label="Close"',
            ],
            'card' => [
                'root'   => 'card',
                'header' => 'card-header fw-semibold',
                'body'   => 'card-body',
                'footer' => 'card-footer text-muted',
            ],

        ];
    }

    private function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return implode(', ', [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ]);
    }
}
