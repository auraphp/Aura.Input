<?php
declare(strict_types=1);

/**
 *
 * This file is part of the Aura project for PHP.
 *
 * @package Aura.Input
 *
 * @license http://opensource.org/licenses/MIT-license.php MIT
 *
 */
namespace Aura\Input;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Aura\Filter_Interface\FailuresInterface;
use Aura\Input\Filter\FailureCollection;

/**
 *
 * Represents a collection of fieldsets of a single type.
 *
 * @package Aura.Input
 *
 */
class Collection extends AbstractInput implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Factory to create a particular fieldset type.
     *
     * @var callable
     */
    protected $factory;

    /**
     * Fieldsets in the collection.
     *
     * @var Fieldset[]
     */
    protected array $fieldsets = [];

    /**
     * Constructor.
     *
     * @param callable $factory A factory to create the fieldset objects for
     * this collection.
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Support for this input when addressed via Fieldset::__set().
     *
     * @param array $data The data for each fieldset in the collection.
     */
    public function fill(array $data): void
    {
        $this->fieldsets = [];
        foreach ($data as $key => $inputs) {
            $fieldset = $this->newFieldset($key);
            foreach ($inputs as $name => $value) {
                $fieldset->getInput($name)->fill($value);
            }
            $this->fieldsets[$key] = $fieldset;
        }
    }

    /**
     * Applies each fieldset filter.
     *
     * @return bool True if all filters passed, false if one or more failed.
     */
    public function filter(): bool
    {
        $passed = true;
        foreach ($this->fieldsets as $fieldset) {
            if (! $fieldset->filter()) {
                $passed = false;
            }
        }
        return $passed;
    }

    /**
     * Returns the failures for all fieldset filters as a flat FailuresInterface,
     * using dot-notation keys: "{index}.{field}" (e.g. "0.number").
     */
    public function getFailures(): FailuresInterface
    {
        $collector = new FailureCollection();
        foreach ($this->fieldsets as $key => $fieldset) {
            foreach ($fieldset->getFailures()->getMessages() as $field => $messages) {
                $collector->addMessagesForField("{$key}.{$field}", $messages);
            }
        }
        return $collector;
    }

    /**
     * IteratorAggregate: returns an external iterator for this collection.
     */
    public function getIterator(): CollectionIterator
    {
        return new CollectionIterator($this);
    }

    /**
     * Gets all the keys for all Fieldsets in this collection.
     *
     * @return array<int|string>
     */
    public function getKeys(): array
    {
        return array_keys($this->fieldsets);
    }

    /**
     * Creates and returns a new fieldset.
     *
     * @param int|string $key The key for the new fieldset.
     */
    protected function newFieldset(int|string $key): Fieldset
    {
        $factory  = $this->factory;
        $fieldset = $factory();
        $fieldset->setName((string) $key);
        return $fieldset;
    }

    /**
     * ArrayAccess: returns the fieldset at a particular offset.
     */
    public function offsetGet(mixed $offset): Fieldset
    {
        $fieldset = $this->fieldsets[$offset];
        $fieldset->setNamePrefix($this->getFullName());
        return $fieldset;
    }

    /**
     * ArrayAccess: sets an offset as a Fieldset.
     */
    public function offsetSet(mixed $offset, mixed $fieldset): void
    {
        $this->fieldsets[$offset] = $fieldset;
    }

    /**
     * ArrayAccess: is a particular Fieldset key set?
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->fieldsets[$offset]);
    }

    /**
     * ArrayAccess: unsets a particular Fieldset key.
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->fieldsets[$offset]);
    }

    /**
     * Countable: returns the number of Fieldsets in this collection.
     */
    public function count(): int
    {
        return count($this->fieldsets);
    }

    /**
     * Returns the value of this input for use in arrays.
     *
     * @return array<int|string, mixed>
     */
    public function getValue(): array
    {
        $data = [];
        foreach ($this->fieldsets as $key => $fieldset) {
            $data[$key] = $fieldset->getValue();
        }
        return $data;
    }
}
