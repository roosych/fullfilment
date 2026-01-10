<?php
if (! function_exists('active_link')) {
    function active_link(array $route, string $active = 'here show'): string
    {
        return request()->routeIs($route) ? $active : '';
    }
}

if (! function_exists('money')) {
    function money(?float $value, string $currency = '₼'): string
    {
        if ($value === null) {
            return '0.00 ' . $currency;
        }

        return number_format($value / 100, 2, '.', ' ') . ' ' . $currency;
    }
}
