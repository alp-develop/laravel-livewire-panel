<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Themes;

final class Bootstrap4Theme extends AbstractTheme
{
    public function id(): string
    {
        return 'bootstrap4';
    }

    /** @return list<string> */
    public function cssAssets(): array
    {
        return [
            'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css',
        ];
    }

    /** @return list<string> */
    public function jsAssets(): array
    {
        return [
            'https://code.jquery.com/jquery-3.7.1.slim.min.js',
            'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js',
        ];
    }

    /** @param array<string, mixed> $styleConfig */
    public function headHtml(array $styleConfig = []): string
    {
        $c = $this->resolveThemeColors($styleConfig);
        $primaryRgb = $this->primaryRgb($c['primary']);

        $html = <<<HTML
<style>
.btn-primary{background-color:{$c['primary']};border-color:{$c['primary']};}.btn-primary:hover,.btn-primary:focus{background-color:{$c['primary']};border-color:{$c['primary']};filter:brightness(0.9);}
.btn-secondary{background-color:{$c['secondary']};border-color:{$c['secondary']};}.btn-secondary:hover{background-color:{$c['secondary']};filter:brightness(0.9);}
.btn-success{background-color:{$c['success']};border-color:{$c['success']};}.btn-success:hover{background-color:{$c['success']};filter:brightness(0.9);}
.btn-danger{background-color:{$c['danger']};border-color:{$c['danger']};}.btn-danger:hover{background-color:{$c['danger']};filter:brightness(0.9);}
.btn-warning{background-color:{$c['warning']};border-color:{$c['warning']};}.btn-warning:hover{background-color:{$c['warning']};filter:brightness(0.9);}
.btn-info{background-color:{$c['info']};border-color:{$c['info']};}.btn-info:hover{background-color:{$c['info']};filter:brightness(0.9);}
.badge-primary{background-color:{$c['primary']};}
.badge-secondary{background-color:{$c['secondary']};}
.badge-success{background-color:{$c['success']};}
.badge-danger{background-color:{$c['danger']};}
.badge-warning{background-color:{$c['warning']};}
.badge-info{background-color:{$c['info']};}
.alert-primary{border-color:{$c['primary']};color:{$c['primary']};}
.alert-success{border-color:{$c['success']};color:{$c['success']};}
.alert-danger{border-color:{$c['danger']};color:{$c['danger']};}
.alert-warning{border-color:{$c['warning']};color:{$c['warning']};}
.alert-info{border-color:{$c['info']};color:{$c['info']};}
body{font-family:{$c['font']};}
.btn,.card,.badge{border-radius:{$c['radius']};}
a{color:{$c['primary']};}
a:hover{color:{$c['primary']};filter:brightness(0.85);}
.form-control:focus{border-color:{$c['primary']};box-shadow:0 0 0 .2rem rgba({$primaryRgb},.25);}
.custom-select:focus{border-color:{$c['primary']};box-shadow:0 0 0 .2rem rgba({$primaryRgb},.25);}
.custom-control-input:checked~.custom-control-label::before{background-color:{$c['primary']};border-color:{$c['primary']};}
.custom-control-input:focus~.custom-control-label::before{box-shadow:0 0 0 .2rem rgba({$primaryRgb},.25);}
.page-item.active .page-link{background-color:{$c['primary']};border-color:{$c['primary']};}
input,select,textarea{accent-color:{$c['primary']}!important;caret-color:{$c['primary']};}
.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable{background-color:{$c['primary']}!important;}
.select2-container--default .select2-results__option--selected{background-color:rgba({$primaryRgb},.15)!important;}
.select2-container--default.select2-container--focus .select2-selection--single,.select2-container--default.select2-container--focus .select2-selection--multiple{border-color:{$c['primary']}!important;}
.select2-container--default.select2-container--open .select2-selection--single,.select2-container--default.select2-container--open .select2-selection--multiple{border-color:{$c['primary']}!important;}
.select2-container--default .select2-selection--multiple .select2-selection__choice{background-color:{$c['primary']}!important;border-color:{$c['primary']}!important;color:#fff;}
.flatpickr-day.selected,.flatpickr-day.startRange,.flatpickr-day.endRange,.flatpickr-day.selected.inRange{background:{$c['primary']}!important;border-color:{$c['primary']}!important;}
.flatpickr-day.selected:hover,.flatpickr-day.startRange:hover,.flatpickr-day.endRange:hover{background:{$c['primary']}!important;border-color:{$c['primary']}!important;filter:brightness(0.9);}
.flatpickr-day.today{border-color:{$c['primary']}!important;}
.flatpickr-day.today:hover{background:{$c['primary']}!important;border-color:{$c['primary']}!important;color:#fff;}
</style>
HTML;

        if (!empty($styleConfig['layout']['dark_mode'])) {
            $d = $this->resolveDarkColors($styleConfig);

            $html .= '<style>'
                . 'html[data-theme="dark"] .card{background-color:' . $d['surface'] . ';border-color:' . $d['border'] . ';color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .card-header{background-color:transparent;border-color:' . $d['border'] . ';color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .card-footer{background-color:transparent;border-color:' . $d['border'] . ';color:' . $d['text_muted'] . '}'
                . 'html[data-theme="dark"] .table{color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .table th,html[data-theme="dark"] .table td{border-color:' . $d['border'] . '}'
                . 'html[data-theme="dark"] .table-striped tbody tr:nth-of-type(odd){background-color:rgba(255,255,255,0.03)}'
                . 'html[data-theme="dark"] .table-hover tbody tr:hover{background-color:rgba(255,255,255,0.05)}'
                . 'html[data-theme="dark"] .form-control{background-color:' . $d['surface'] . ';border-color:' . $d['border'] . ';color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .form-control:focus{background-color:' . $d['surface'] . ';border-color:' . $c['primary'] . ';color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .input-group-text{background-color:' . $d['background'] . ';border-color:' . $d['border'] . ';color:' . $d['text_muted'] . '}'
                . 'html[data-theme="dark"] .modal-content{background-color:' . $d['surface'] . ';border-color:' . $d['border'] . ';color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .modal-header,html[data-theme="dark"] .modal-footer{border-color:' . $d['border'] . '}'
                . 'html[data-theme="dark"] .list-group-item{background-color:' . $d['surface'] . ';border-color:' . $d['border'] . ';color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .dropdown-menu{background-color:' . $d['surface'] . ';border-color:' . $d['border'] . '}'
                . 'html[data-theme="dark"] .dropdown-item{color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .dropdown-item:hover,html[data-theme="dark"] .dropdown-item:focus{background-color:rgba(255,255,255,0.06);color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .custom-select{background-color:' . $d['surface'] . ';border-color:' . $d['border'] . ';color:' . $d['text'] . '}'
                . 'html[data-theme="dark"] .page-link{background-color:' . $d['surface'] . ';border-color:' . $d['border'] . ';color:' . $d['text'] . '}'
                . '</style>';
        }

        return $html;
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
                'header' => 'card-header font-weight-bold',
                'body'   => 'card-body',
                'footer' => 'card-footer text-muted',
            ],

        ];
    }

    private function primaryRgb(string $hex): string
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
