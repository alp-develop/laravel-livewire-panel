<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Cdn;

interface CdnManagerInterface
{
    /**
     * @param  array<string, mixed> $panelConfig
     * @return array{css: list<array{url: string, integrity: string}>, js: list<array{url: string, integrity: string}>}
     */
    public function resolveForPanel(array $panelConfig, string $currentPath): array;

    /**
     * @param  array<string, mixed> $panelConfig
     * @return list<string>
     */
    public function activeAliases(array $panelConfig, string $currentPath): array;

    /**
     * @param array<string, mixed> $panelConfig
     */
    public function renderCssLinks(array $panelConfig, string $currentPath): string;

    /**
     * @param array<string, mixed> $panelConfig
     */
    public function renderJsScripts(array $panelConfig, string $currentPath): string;
}
