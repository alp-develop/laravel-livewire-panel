<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Compat;

final class LivewireVersion
{
    private static ?int $major = null;

    public static function major(): int
    {
        if (self::$major !== null) {
            return self::$major;
        }

        if (defined('\\Livewire\\Livewire::VERSION')) {
            self::$major = (int) explode('.', (string) \Livewire\Livewire::VERSION)[0];
        } elseif (class_exists(\Livewire\Livewire::class)) {
            $version = \Composer\InstalledVersions::getPrettyVersion('livewire/livewire') ?? '3.0.0';
            self::$major = (int) explode('.', $version)[0];
        } else {
            self::$major = 3;
        }

        return self::$major;
    }

    public static function isV3(): bool
    {
        return self::major() === 3;
    }

    public static function isV4OrAbove(): bool
    {
        return self::major() >= 4;
    }

    public static function reset(): void
    {
        self::$major = null;
    }
}
