# Themes

## Built-in themes

| ID | CSS Framework | CDN |
|---|---|---|
| `bootstrap5` | Bootstrap 5.3 | jsDelivr |
| `bootstrap4` | Bootstrap 4.6 | jsDelivr |
| `tailwind` | Tailwind CSS 3 (CDN play) | jsDelivr |

---

## Switching themes

Set `theme` in the panel config:

```php
'panels' => [
    'admin'  => ['theme' => 'bootstrap5'],
    'client' => ['theme' => 'tailwind'],
    'legacy' => ['theme' => 'bootstrap4'],
],
```

Each panel can use a different theme independently.

---

## Creating a custom theme

### 1. Generate the class

```bash
php artisan panel:make-theme MyTheme
```

This creates `app/Panel/Themes/MyTheme.php`.

### 2. Implement `ThemeInterface`

Extend `AbstractTheme` for the easiest path:

```php
namespace App\Panel\Themes;

use AlpDevelop\LivewirePanel\Themes\AbstractTheme;

final class MyTheme extends AbstractTheme
{
    public function id(): string
    {
        return 'my-theme';
    }

    public function cssAssets(): array
    {
        return [
            'https://cdn.example.com/my-framework.min.css',
        ];
    }

    public function jsAssets(): array
    {
        return [
            'https://cdn.example.com/my-framework.min.js',
        ];
    }
}
```

### 3. Register the theme

In your `AppServiceProvider::boot()`:

```php
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;
use App\Panel\Themes\MyTheme;

$themes = $this->app->make(ThemeRegistry::class);
$themes->register('my-theme', MyTheme::class);
```

### 4. Use it

```php
// config/laravel-livewire-panel.php
'theme' => 'my-theme',
```

---

## ThemeInterface methods

| Method | Return | Description |
|---|---|---|
| `id(): string` | `string` | Unique theme identifier |
| `cssAssets(): array` | `string[]` | CDN CSS URLs |
| `jsAssets(): array` | `string[]` | CDN JS URLs |
| `headHtml(array $styleConfig = []): string` | `string` | Extra HTML injected in `<head>` |
| `cssVariables(array $styleConfig): string` | `string` | Generate CSS variable block from style config |
| `componentClasses(): array` | `array` | Map of component → slot → CSS classes |

---

## Blade components

All Blade components use the `x-panel::` prefix and are theme-agnostic. Each theme provides the correct HTML structure for its CSS framework.

| Component | Usage |
|---|---|
| `x-panel::button` | `<x-panel::button variant="primary" size="md">Click</x-panel::button>` |
| `x-panel::card` | `<x-panel::card title="My Card">Content</x-panel::card>` |
| `x-panel::alert` | `<x-panel::alert variant="warning">Warning text</x-panel::alert>` |
| `x-panel::icon` | `<x-panel::icon name="home" size="20" />` |
| `x-panel::portal` | `<x-panel::portal panel="operator" :user="$user">Go to Operator</x-panel::portal>` |
