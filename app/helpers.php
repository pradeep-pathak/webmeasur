<?php

if (!function_exists('formatMilliseconds')) {
    function formatMilliseconds($milliseconds)
    {
        $interval = \Carbon\CarbonInterval::milliseconds($milliseconds);
        return $interval->cascade()->forHumans(['short' => true]);
    }
}

if (!function_exists('formatSeconds')) {
    function formatSeconds($seconds)
    {
        $interval = \Carbon\CarbonInterval::seconds($seconds);
        return $interval->cascade()->forHumans(['short' => true]);
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber(int $number)
    {
        if ($number >= 1000) {
            return number_format($number / 1000, 2) . 'k';
        } else {
            return $number;
        }
    }
}

if (!function_exists('addGetParams')) {
    function addGetParams($routeName, $newParams = [])
    {
        $currentParams = request()->query();
        $mergedParams = array_merge($currentParams, $newParams);
        return route($routeName, $mergedParams);
    }
}

if (!function_exists('removeGetParams')) {
    function removeGetParams($params)
    {
        $currentParams = request()->query();

        foreach ($params as $param) {
            unset($currentParams[$param]);
        }

        return url()->current() . '?' . http_build_query($currentParams);
    }
}
