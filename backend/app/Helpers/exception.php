<?php

use Symfony\Component\HttpKernel\Exception\HttpException;

if (!function_exists('throwIf')) {
    function throwIf(bool $condition, string $message, int $status = 400): void
    {
        if ($condition) {
            throw new HttpException($status, $message);
        }
    }
}
