<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

final class StubResolver
{
    /** @param array<string, string> $replacements */
    public static function resolve(string $stub, array $replacements): string
    {
        $publishedPath = base_path('stubs/panel/' . $stub);
        $defaultPath   = __DIR__ . '/../../resources/stubs/' . $stub;

        $path    = file_exists($publishedPath) ? $publishedPath : $defaultPath;
        $content = (string) file_get_contents($path);

        return strtr($content, $replacements);
    }
}
