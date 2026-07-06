<?php

namespace App\Services;

class PasswordGenerator
{
    public static function generate(int $length = 8): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';

        return collect(range(1, $length))
            ->map(fn () => $characters[random_int(0, strlen($characters) - 1)])
            ->implode('');
    }
}