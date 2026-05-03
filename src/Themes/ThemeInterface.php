<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Themes;

interface ThemeInterface
{
    public function id(): string;

    /** @return list<string> */
    public function cssAssets(): array;

    /** @return list<string> */
    public function jsAssets(): array;

    /** @param array<string, mixed> $styleConfig */
    public function headHtml(array $styleConfig = []): string;

    /** @param array<string, mixed> $styleConfig */
    public function cssVariables(array $styleConfig): string;

    /** @param array<string, mixed> $styleConfig */
    public function darkCssVariables(array $styleConfig): string;

    /** @return array<string, array<string, string>> */
    public function componentClasses(): array;
}
