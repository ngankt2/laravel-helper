<?php
if (!function_exists('_get_max_upload_support')) {
    /**
     * @param float|int $max is Kilobyte
     * @return string|int|float
     */
    function _get_max_upload_support(float|int $max = 2 * 1024): string|int|float
    {

        $serverMaxSize = return_kilobytes(ini_get('upload_max_filesize'));
        if ($max > $serverMaxSize) {
            return $serverMaxSize;
        }
        return $max;
    }
}
if (!function_exists('return_kilobytes')) {
    /**
     * @param float|int $max is Kilobyte
     * @return string|int|float
     */
    function return_kilobytes($val): int|string
    {
        $val  = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        return match ($last) {
            'g' => (float)$val * 1024 * 1024,
            'm' => (float)$val * 1024,
            default => (float)$val,
        };
    }
}

if (!function_exists('zi_format_currency')) {

    function zi_format_currency($amount, $symbol = '₫'): string
    {
        return number_format($amount, 0, ',', '.') . ' <sup>' . $symbol . '</sup>';
    }
}

if (!function_exists('zi_format_currency_vnd')) {

    function zi_format_currency_vnd($amount): string
    {
        return number_format($amount, 0, ',', '.');
    }
}







