# Rector Pest

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mrpunyapal/rector-pest.svg?style=flat-square)](https://packagist.org/packages/mrpunyapal/rector-pest)

Rector rules for [PestPHP](https://pestphp.com/) to improve code quality and help with version upgrades.

## Installation

```bash
composer require --dev mrpunyapal/rector-pest
```

## Available Rule Sets

### Code Quality

Improve your Pest tests with better readability and expressiveness.

```php
// rector.php
use MrPunyapal\RectorPest\Set\PestSetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/tests',
    ])
    ->withSets([
        PestSetList::PEST_CODE_QUALITY,
    ]);
```

**Included Rules:**

| Rule | Description |
|------|-------------|
| `ChainExpectCallsRector` | Chains multiple `expect()` calls on the same value into a single chained call (e.g. `expect($value)->toBe(10)->toBeInt()`) |
| `SimplifyExpectNotRector` | Converts `expect(!$x)->toBeTrue()` to `expect($x)->not->toBeTrue()` |
| `ToBeTrueNotFalseRector` | Simplifies `->not->toBeFalse()` to `->toBeTrue()` and vice versa |
| `UseEachModifierRector` | Converts `foreach` loops with `expect()` to `->each` modifier |
| `SimplifyToLiteralBooleanRector` | Converts `->toBe(true)` to `->toBeTrue()` (and `false`, `null`, `[]`) |
| `UseTypeMatchersRector` | Converts `expect(is_array($x))->toBeTrue()` to `expect($x)->toBeArray()` |
| `UseToHaveCountRector` | Converts `expect(count($arr))->toBe(5)` to `expect($arr)->toHaveCount(5)` |
| `UseInstanceOfMatcherRector` | Converts `expect($obj instanceof User)->toBeTrue()` to `expect($obj)->toBeInstanceOf(User::class)` |
| `SimplifyComparisonExpectationsRector` | Converts `expect($x > 10)->toBeTrue()` to `expect($x)->toBeGreaterThan(10)` |
| `UseToMatchRector` | Converts `expect(preg_match('/pattern/', $str))->toBe(1)` to `expect($str)->toMatch('/pattern/')` |

## Automate Pest Upgrades

Use `PestLevelSetList` to automatically upgrade to a specific Pest version. Sets for higher versions include sets for lower versions.

```php
// rector.php
use MrPunyapal\RectorPest\Set\PestLevelSetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/tests',
    ])
    ->withSets([
        PestLevelSetList::UP_TO_PEST_40,
    ]);
```

### Manual Version Configuration

Use `PestSetList` if you only want changes for a specific version:

```php
// rector.php
use MrPunyapal\RectorPest\Set\PestSetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/tests',
    ])
    ->withSets([
        PestSetList::PEST_30, // Only v2→v3 changes
    ]);
```

### Pest v3 (PEST_30)

Rules for upgrading from Pest v2 to v3:

| Rule | Description |
|------|-------------|
| `TapToDeferRector` | Replaces deprecated `->tap()` with `->defer()` |
| `ToHaveMethodOnClassRector` | Changes `expect($object)->toHaveMethod()` to `expect($object::class)->toHaveMethod()` |

### Pest v4 (PEST_40)

Rules for upgrading from Pest v3 to v4:

> **Note:** Pest v4 primarily requires dependency updates (PHPUnit 12, PHP 8.3+) with minimal code changes. Rules will be added as migration patterns emerge.

## Individual Rules

You can also use individual rules:

```php
// rector.php
use MrPunyapal\RectorPest\Rules\ChainExpectCallsRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/tests',
    ])
    ->withRules([
        ChainExpectCallsRector::class,
    ]);
```

## Rule Examples

### ChainExpectCallsRector

```php
// Before
expect($value)->toBe(10);
expect($value)->toBeInt();

// After
expect($value)->toBe(10)->toBeInt();
```

### SimplifyExpectNotRector

```php
// Before
expect(!$condition)->toBeTrue();

// After
expect($condition)->not->toBeTrue();
```

### ToBeTrueNotFalseRector

```php
// Before
expect($value)->not->toBeFalse();

// After
expect($value)->toBeTrue();
```

### UseEachModifierRector

```php
// Before
foreach ($items as $item) {
    expect($item)->toBeString();
}

// After
expect($items)->each->toBeString();
```

### TapToDeferRector (v3)

```php
// Before
expect($value)->tap(fn ($v) => dump($v))->toBe(10);

// After
expect($value)->defer(fn ($v) => dump($v))->toBe(10);
```

### ToHaveMethodOnClassRector (v3)

```php
// Before
expect($user)->toHaveMethod('getName');

// After
expect($user::class)->toHaveMethod('getName');
```

### SimplifyToLiteralBooleanRector

```php
// Before
expect($value)->toBe(true);
expect($value)->toBe(false);
expect($value)->toBe(null);
expect($array)->toEqual([]);

// After
expect($value)->toBeTrue();
expect($value)->toBeFalse();
expect($value)->toBeNull();
expect($array)->toBeEmpty();
```

### UseTypeMatchersRector

```php
// Before
expect(is_array($value))->toBeTrue();
expect(is_string($value))->toBeTrue();

// After
expect($value)->toBeArray();
expect($value)->toBeString();
```

### UseToHaveCountRector

```php
// Before
expect(count($array))->toBe(5);

// After
expect($array)->toHaveCount(5);
```

### UseInstanceOfMatcherRector

```php
// Before
expect($user instanceof User)->toBeTrue();

// After
expect($user)->toBeInstanceOf(User::class);
```

### SimplifyComparisonExpectationsRector

```php
// Before
expect($value > 10)->toBeTrue();
expect($value >= 10)->toBeTrue();
expect($value < 5)->toBeTrue();
expect($value <= 5)->toBeTrue();

// After
expect($value)->toBeGreaterThan(10);
expect($value)->toBeGreaterThanOrEqual(10);
expect($value)->toBeLessThan(5);
expect($value)->toBeLessThanOrEqual(5);
```

### UseToMatchRector

```php
// Before
expect(preg_match('/pattern/', $string))->toBe(1);

// After
expect($string)->toMatch('/pattern/');
```

## Running Rector

```bash
# Preview changes
vendor/bin/rector process --dry-run

# Apply changes
vendor/bin/rector process
```

## Requirements

- PHP 8.2+
- Rector 2.0+

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
