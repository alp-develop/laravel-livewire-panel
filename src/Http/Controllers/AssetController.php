<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Http\Controllers;

use Illuminate\Http\Response;

final class AssetController
{
    public function __invoke(string $file): Response
    {
        $basePath = realpath(dirname(__DIR__, 3) . '/resources/assets');

        if ($basePath === false) {
            abort(404);
        }

        $filePath = realpath($basePath . '/' . $file);

        if ($filePath === false || !str_starts_with($filePath, $basePath) || !is_file($filePath)) {
            abort(404);
        }

        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $contentType = match ($ext) {
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
            default => 'text/plain',
        };

        $content = file_get_contents($filePath);

        if ($content === false) {
            abort(404);
        }

        return response($content)
            ->header('Content-Type', $contentType)
            ->header('Cache-Control', 'public, max-age=604800');
    }
}
