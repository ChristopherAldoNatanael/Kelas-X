<?php

if (!function_exists('public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param  string  $path
     * @return string
     */
    function public_path($path = '')
    {
        return rtrim(app()->basePath('public'), '/') . ($path ? '/' . $path : $path);
    }
}
