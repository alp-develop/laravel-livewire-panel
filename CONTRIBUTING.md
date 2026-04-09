# Contributing

Thank you for considering contributing to Laravel Livewire Panel.

## Development Setup

1. Fork and clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`

## Workflow

1. Create a branch from `main` for your change
2. Make your changes following the project architecture
3. Add or update tests as needed
4. Ensure all checks pass:
   - `composer test` -- All tests pass
   - `vendor/bin/phpstan analyse` -- PHPStan level 8
5. Submit a pull request to `main`

## Architecture Rules

- **Core** (`src/`) -- Service providers, kernel, resolver, renderer, config.
- **Modules** (`src/Modules/`) -- Self-contained features with routes, navigation, Livewire components.
- **Themes** (`src/Themes/`) -- Driver pattern via `ThemeInterface`.
- **Widgets** (`src/Widgets/`) -- Livewire-based widgets with `WidgetInterface`.
- **Plugins** (`src/Plugins/`) -- Cross-panel extensions with lifecycle hooks.
- Do not mix responsibilities between layers.
- Do not modify existing contracts -- extend them instead.

## Code Standards

- PHP 8.1+ compatible
- Strict types in all files
- No comments in code -- use clear, self-documenting names
- Final classes by default, abstract only when designed for extension
- Readonly constructor properties where possible
- PSR-12 code style

## Tests

- Use Pest with Orchestra Testbench
- Place feature tests in `tests/Feature/`
- All new features must include tests

## Pull Request Guidelines

- One feature or fix per PR
- Include a clear description of what changed and why
- Reference any related issues
- Ensure CI passes before requesting review
