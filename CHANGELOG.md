# CHANGELOG

## 6.0.0

### Breaking Changes

- PHP 8.4+ is now required (up from PHP >= 5.6 in the last released 2.x line).
- License changed from BSD to MIT.
- Removed `aura/installer-default` from `composer.json`.
- Removed Aura.Di configuration files.
- `Filter::apply()` no longer mutates `$values` by reference and no longer returns `bool`. It now accepts `array|object` by value and returns a `FilterResultInterface` containing the success flag, the values, and a `FailureCollectionInterface`. See the upgrade guide for migration details.
- `Fieldset::filter()` and `Collection::filter()` now return `bool` with an explicit return-type declaration; `getFailures()` now returns `Aura\Filter_Interface\FailuresInterface` (typed).
- `Collection::getFailures()` now returns a flat `FailuresInterface` keyed with dot-notation paths (e.g. `"0.email"`) instead of a nested array.
- `CollectionIterator` methods (`current()`, `key()`, `next()`, `rewind()`, `valid()`) now carry explicit return-type declarations; properties are typed.
- All classes now declare `strict_types=1` and use full PHP 8.x type declarations throughout.
- Removed Aura.Di integration — no `config/` directory or container configuration is shipped.
- `aura/filter-interface` dependency updated to `dev-sub-filter` (introduces `FilterInterface`, `FilterResultInterface`, `FailureCollectionInterface`, `FailuresInterface`, `FailureInterface`, and `FilterResult`).
- CI moved from Travis CI to GitHub Actions; PHPUnit dependency changed from `yoast/phpunit-polyfills` to `phpunit/phpunit: ^11.0`.

### New Features

- ADD: `Filter\Failure` — concrete `FailureInterface` implementation for closure-based filter rules. Uses PHP 8 readonly constructor-promoted properties. Implements `JsonSerializable` via `FailureInterface`.
- ADD: `Filter\FailureCollection` — implements the full `FailureCollectionInterface` (both the write side `add()` / `set()` and the read side `forField()` / `forPath()` / `getMessages()` / `getNestedMessages()`). Stores plain string messages; wraps them in `Failure` value objects on demand.
- ADD: `FailureCollection::getNestedMessages()` — reconstructs a nested array structure from flat dot-notation keys, mirroring the shape of the original input data.
- ADD: `FailureCollection::forPath(string $path)` — dot-notation path lookup returning `FailureInterface[]`.
- ADD: `FailureCollection::isEmpty()` — returns `true` when no failures are recorded.
- ADD: `Filter::apply()` now returns `FilterResultInterface` (a `FilterResult` value object) containing `isSuccess()`, `getValues()`, and `getFailures()`.
- ADD: PHP 8.2, 8.3, 8.4, and 8.5 support added to CI test matrix.
- ADD: `aura/filter-interface` package dependency introducing `SubjectFilterInterface`, `FilterResultInterface`, `FilterResult`, and `FailureInterface`.

### Changes

- CHG: `Filter::setRule()` and `Filter::addRule()` parameter types are now `string $field`, `string $message`, `\Closure $closure` (strict types).
- CHG: `Filter::getMessages()` parameter is now `?string $field = null` (typed); return type is `array`.
- CHG: `Filter::addMessages()` parameter `$messages` is now `string|array` (union type).
- CHG: `Fieldset::filter()` now consumes `FilterResultInterface` from `Filter::apply()` and aggregates nested fieldset/collection failures automatically using dot-notation prefixing.
- CHG: `Fieldset::getFailures()` return type is now declared as `FailuresInterface`.
- CHG: `Collection::fill()` return type is now declared as `void`.
- CHG: `Collection::filter()` return type is now declared as `bool`.
- CHG: `Collection::getFailures()` return type is now declared as `FailuresInterface`.
- CHG: License header updated to MIT throughout all source files.
- DOC: README updated with PHP version requirements.

## 1.2.1

- Fixed addMessages behavior in Filter class, see #64

## 1.2.0

- Support for adding messages in closure rule, see https://github.com/auraphp/Aura.Input/pull/40
- New `isSuccess` method, see https://github.com/auraphp/Aura.Input/pull/45
- Branch alias `dev-develop` removed from `composer.json`, see https://github.com/auraphp/Aura.Input/commit/02e125148dcb23813799f8fe0bb8a70d66706b45
- Tests are now running on 5.4, 5.5, 5.6, 7.0 and HHVM on travis-ci.

See the [1.x branch CHANGES.md](https://github.com/auraphp/Aura.Input/blob/1.x/CHANGES.md) for the full 1.x release history, including versions 1.0.0 through 1.2.1.
