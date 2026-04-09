<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Security;

final class SearchQuerySanitizer
{
    public static function sanitize(string $query, int $maxLength = 100): string
    {
        $query = mb_substr($query, 0, $maxLength);

        return str_replace(['%', '_'], ['\%', '\_'], $query);
    }
}
