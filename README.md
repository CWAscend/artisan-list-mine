# Artisan List Mine

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ascend/artisan-list-mine.svg?style=flat-square)](https://packagist.org/packages/ascend/artisan-list-mine)
[![Tests](https://github.com/ascend-digital/artisan-list-mine/actions/workflows/tests.yml/badge.svg)](https://github.com/ascend-digital/artisan-list-mine/actions/workflows/tests.yml)
[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue?style=flat-square)](composer.json)
[![Laravel Version](https://img.shields.io/badge/laravel-9%20%7C%2010%20%7C%2011%20%7C%2012-red?style=flat-square)](composer.json)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)

Filter artisan commands to show only your application commands, excluding vendor packages.

## Installation

```bash
composer require ascend/artisan-list-mine
```

The package will automatically register itself via Laravel's package discovery.

## Usage

Use the `--mine` flag with the `list` command to show only your application's commands:

```bash
php artisan list --mine
```

This filters out:
- Built-in Laravel commands (`make:*`, `migrate`, etc.)
- Commands from vendor packages
- Any command not defined in your `App\` namespace

### Example Output

```
Laravel Framework 12.x

Usage:
  command [options] [arguments]

Options:
  ...
  --mine            Only show application commands (excludes vendor)
  ...

Available commands:
 app
  app:sync-data        Synchronize data from external API
  app:generate-report  Generate monthly reports
 orders
  orders:process       Process pending orders
```

## How It Works

The package identifies "application commands" by:

1. Checking if the command class is in the `App\` namespace
2. Checking if the command has a handler/action in the `App\` namespace (for closure-based or action-based commands)

## Requirements

- PHP 8.0+
- Laravel 9.0+

## Testing

```bash
composer test
```

## License

MIT License. See [LICENSE](LICENSE) for more information.
