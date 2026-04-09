<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Themes;

interface ThemeInterface
{
    public function id(): string;

    public function cssAssets(): array;

    public function jsAssets(): array;

    public function headHtml(array $styleConfig = []): string;

    public function cssVariables(array $styleConfig): string;

    public function componentClasses(): array;
}
