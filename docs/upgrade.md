# Upgrade Guide: 1.x → 7.0.0

This guide covers every breaking change introduced in 7.0.0 and how to update your code. The last publicly released version of this package was the **2.x / 1.x** line; 7.0.0 is the next release after the unreleased `3.0.0-alpha`.

---

## PHP Version Requirement

**2.x required PHP >= 5.6. 7.0.0 requires PHP >= 8.4.**

Ensure your runtime and CI pipeline are running PHP 8.4 or later before upgrading.

---

## New Dependency: `aura/filter-interface`

7.0.0 requires `aura/filter-interface: 7.x`. This package provides the contracts used throughout Aura.Input:

| Interface / Class | Description |
|---|---|
| `FilterInterface` | Implemented by `Filter` |
| `FilterResultInterface` | Returned by `Filter::apply()` |
| `FilterResult` | Concrete implementation of `FilterResultInterface` |
| `FailureCollectionInterface` | Write-side contract for collecting failures |
| `FailuresInterface` | Read-side contract; returned by `getFailures()` |
| `FailureInterface` | Implemented by `Filter\Failure` |

---

## Removed: Aura.Di Integration

There are no longer any `config/` files or Aura.Di container configuration shipped with this package. Instantiate classes directly or wire them with your own DI container:

```php
use Aura\Input\Builder;
use Aura\Input\Filter;
use Aura\Input\Form;

$form = new Form(new Builder([]), new Filter());
```

---

## `Filter::apply()` — Return Type and Signature Changed

This is the most significant breaking change.

**Before (2.x):**
`apply()` mutated `$values` by reference and returned a `bool`.

```php
// Old: pass-by-reference, returns bool
if ($this->filter->apply($this)) {
    // passed
} else {
    $failures = $this->filter->getFailures();
}
```

**After (7.0.0):**
`apply()` accepts `array|object` by value and returns a `FilterResultInterface`. For `Fieldset` subjects the object handle still allows closures to write field values back — but the caller's variable is never replaced by reference.

```php
// New: returns FilterResultInterface
$result = $this->filter->apply($this);

if ($result->isSuccess()) {
    // all rules passed
} else {
    $failures = $result->getFailures(); // FailuresInterface
}
```

If you write a custom `Filter` subclass, update the `apply()` method signature and return value:

```php
// Before
public function apply(&$values): bool { ... }

// After
public function apply(array|object $values): FilterResultInterface { ... }
```

---

## `Fieldset::filter()` — Now Uses `FilterResultInterface` Internally

`Fieldset::filter()` still returns `bool` and `getFailures()` still returns the aggregated failures — the external behaviour is unchanged. However, if you extended `Fieldset` and overrode `filter()`, update it to consume `FilterResultInterface`:

```php
// Before (2.x override)
public function filter()
{
    $this->success  = $this->filter->apply($this);
    $this->failures = $this->filter->getFailures();
    // ...
    return $this->success;
}

// After (7.0.0 override)
public function filter(): bool
{
    $result        = $this->filter->apply($this);
    $this->success = $result->isSuccess();

    $collector = new \Aura\Input\Filter\FailureCollection();
    foreach ($result->getFailures()->getMessages() as $field => $messages) {
        $collector->addMessagesForField($field, $messages);
    }
    // aggregate nested ...
    $this->failures = $collector;
    return $this->success;
}
```

---

## `getFailures()` Now Returns `FailuresInterface`

`Fieldset::getFailures()` and `Collection::getFailures()` now declare a return type of `Aura\Filter_Interface\FailuresInterface`. If you type-hint the return value, update your code:

```php
// Before
/** @var array|\Aura\Filter_Interface\FailureCollectionInterface */
$failures = $fieldset->getFailures();

// After
/** @var \Aura\Filter_Interface\FailuresInterface */
$failures = $fieldset->getFailures();
```

`FailuresInterface` provides:

```php
$failures->isEmpty();                      // bool
$failures->getMessages();                  // array<string, string[]>
$failures->forField(string $field);        // FailureInterface[]
$failures->forPath(string $path);          // FailureInterface[] (dot-notation)
$failures->getNestedMessages();            // nested array mirroring input shape
```

---

## Nested Failures Now Use Dot-Notation Keys

**Before (2.x):**
When a nested `Fieldset` or `Collection` failed, its failures were stored under the parent field name as a nested array:

```php
// e.g. for a 'phone_numbers' collection with an 'number' field error
$failures->getMessages();
// ['phone_numbers' => ['0' => ['number' => ['Phone number is required.']]]]
```

**After (7.0.0):**
Failures are stored flat with dot-notation keys:

```php
$failures->getMessages();
// ['phone_numbers.0.number' => ['Phone number is required.']]
```

To get the nested structure back, use `getNestedMessages()`:

```php
$failures->getNestedMessages();
// ['phone_numbers' => ['0' => ['number' => ['Phone number is required.']]]]
```

Update any code that accessed nested failures by array key to use dot-notation or `getNestedMessages()`.

---

## Strict Types and Full Type Declarations

All classes now declare `strict_types=1` and carry typed properties and method return types. If you extended any class and overrode methods, add matching type declarations to avoid fatal errors:

```php
// Before (2.x override)
public function fill(array $data) { ... }
public function filter() { ... }

// After (7.0.0 compatible)
public function fill(array $data): void { ... }
public function filter(): bool { ... }
```

---

## `CollectionIterator` — Explicit Return Types

`CollectionIterator` now declares return types on all `Iterator` methods. If you subclassed it, update your overrides:

| Method | Return type |
|---|---|
| `current()` | `Fieldset` |
| `key()` | `int\|string` |
| `next()` | `void` |
| `rewind()` | `void` |
| `valid()` | `bool` |

---

## New: `Filter\Failure` and `Filter\FailureCollection`

Two new concrete classes live in the `Aura\Input\Filter` namespace:

**`Filter\Failure`** — a `FailureInterface` value object for closure-based rules:
```php
$failure->getField();       // string
$failure->getMessage();     // string
$failure->getArgs();        // array (always empty for closure rules)
$failure->jsonSerialize();  // array{field, message, args}
```

**`Filter\FailureCollection`** — full `FailureCollectionInterface` implementation with helpers:
```php
$fc->add('email', 'Invalid email');           // append via interface
$fc->set('email', 'Only this message');       // replace via interface
$fc->addMessagesForField('email', 'Invalid'); // helper used by Filter
$fc->getMessagesForField('email');            // string[]
$fc->forField('email');                       // FailureInterface[]
$fc->forPath('address.city');                 // dot-notation lookup
$fc->getNestedMessages();                     // nested array
$fc->isEmpty();                               // bool
```

---

## PHPUnit Upgrade

The dev dependency has changed from `yoast/phpunit-polyfills` to `phpunit/phpunit: ^11.0`. Update your `composer.json` accordingly if your own tests extend any Aura.Input test infrastructure.
