<?php

class Sanitizer
{
    public static function clean($value)
    {
        if (is_array($value)) {
            return array_map([self::class, 'clean'], $value);
        }

        if ($value === null) {
            return null;
        }

        return trim(strip_tags((string) $value));
    }

    public static function email(string $value): string
    {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }

    public static function int($value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function text(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public static function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);
        $value = preg_replace('/\s+/', '-', $value);
        return trim($value, '-');
    }
}

