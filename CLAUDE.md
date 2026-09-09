# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Overview

Aura.Input is a PHP library for **describing and filtering HTML form inputs**. It deliberately does NOT render forms — it produces "hints" (type/attribs/options/value arrays) that a view layer (e.g. Aura.Html) consumes. Supports nested fieldsets, fieldset collections, a pluggable filter system via `aura/filter-interface`, and a CSRF protection interface.

Development happens on the `7.x` branch (the `main` branch in this repo's terminology).

## Commands

Install dependencies and run the test suite:

```bash
composer install
./vendor/bin/phpunit
```

Run a single test class or method:

```bash
./vendor/bin/phpunit tests/FormTest.php
./vendor/bin/phpunit --filter testFill tests/FormTest.php
```

Run with coverage (matches the CI invocation in [.github/workflows](.github/workflows)):

```bash
php -d xdebug.mode=coverage ./vendor/bin/phpunit --coverage-clover=coverage.xml
```

Run without coverage (faster during iteration):

```bash
./vendor/bin/phpunit --no-coverage
```

There are no Composer scripts (`composer test`, `composer cs-fix`, etc.) defined in [composer.json](composer.json) — invoke PHPUnit directly. CI exercises PHP 7.2–8.5 per [.github/workflows/continuous-integration.yml](.github/workflows/continuous-integration.yml).

## Architecture

The class hierarchy is the key thing to internalize before editing:

```
AbstractInput  (name, name_prefix, getFullName, getValue)
 ├── Field       (single input, type/attribs/options/value hints)
 ├── Collection  (repeated fieldsets of the same type)
 └── Fieldset    (group of inputs; iterable; holds Builder + Filter)
      └── Form   (top-level Fieldset; adds AntiCsrf + fill() guard)
```

- [src/AbstractInput.php](src/AbstractInput.php) carries the name/prefix machinery so nested inputs render as `parent[child]`. `getFullName()` is what propagates the prefix to children via `Fieldset::getInput()`.
- [src/Fieldset.php](src/Fieldset.php) is the workhorse. It holds the `$inputs` array, the injected `BuilderInterface` and `FilterInterface`, and exposes `setField()` / `setFieldset()` / `setCollection()` which all delegate to the builder. `filter()` recurses into nested `Fieldset` and `Collection` children, aggregating failures via `FailureCollectionInterface::addMessagesForField()`. The `init()` hook is the override point for self-initializing subclasses (see [tests/Example/ContactForm.php](tests/Example/ContactForm.php)).
- [src/Form.php](src/Form.php) extends `Fieldset` only to add `setAntiCsrf()` and a `fill()` override that throws `Exception\CsrfViolation` when the CSRF check fails.
- [src/Builder.php](src/Builder.php) is the factory for Fields/Fieldsets/Collections. Fieldset types are registered through a `fieldset_map` of `type => callable($options)`. `newFieldset()` and `newCollection()` look up the callable by type (defaulting `type` to `name`). This is how reusable fieldsets like `AddressFieldset` get wired in — see the "Creating Reusable Fieldsets" section of [docs/index.md](docs/index.md).
- [src/FormFactory.php](src/FormFactory.php) is the same map pattern applied at the Form level: `newInstance($name, $options)` instantiates a registered form factory and forwards options.
- [src/Filter.php](src/Filter.php) is the bundled `FilterInterface` implementation: closure-based rules keyed by field, applied in `apply(&$values)` where `$values` is the Fieldset itself (closures receive `$value` and `$fields`, where `$fields` is the Fieldset and `$value` is `$fields->$field` via `__get` → `Field::read()`). Failures use the bundled [src/Filter/FailureCollection.php](src/Filter/FailureCollection.php), which stores plain string messages per field (via `addMessagesForField`) and also satisfies `FailureCollectionInterface::add/set` by returning [src/Filter/Failure.php](src/Filter/Failure.php) value objects.
- [src/AntiCsrfInterface.php](src/AntiCsrfInterface.php) is an interface only — the library intentionally does not ship a CSRF implementation because it depends on user/session infrastructure outside its scope.

### External contract

The package depends on `aura/filter-interface` (`7.x-dev`) for `FilterInterface` and `FailureCollectionInterface`. Filter swap-outs from other libraries must conform to that interface.

## Layout

- `src/` — PSR-4 `Aura\Input\` namespace.
- `tests/` — PSR-4 `Aura\Input\` namespace (autoload-dev), bootstrapped by [phpunit.php](phpunit.php). [tests/Example/](tests/Example/) contains the canonical fieldset + form + filter usage example covered by `ExampleTest`.
- `docs/index.md` — the user-facing documentation; treat as the primary spec for public API behavior.
