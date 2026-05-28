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

use Iterator;

/**
 *
 * An object to allow iteration over fieldsets.
 *
 * @package Aura.Input
 *
 */
class CollectionIterator implements Iterator
{
    /**
     * The collection over which we are iterating.
     */
    protected Collection $collection;

    /**
     * The keys to iterate over in the fieldsets.
     *
     * @var array<int|string>
     */
    protected array $keys;

    /**
     * Is the current iterator position valid?
     */
    protected bool $valid = false;

    /**
     * Constructor.
     *
     * @param Collection $collection The fieldsets over which to iterate.
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        $this->keys       = $this->collection->getKeys();
    }

    /**
     * Returns the value at the current iterator position.
     */
    public function current(): Fieldset
    {
        return $this->collection->offsetGet($this->key());
    }

    /**
     * Returns the current iterator position key.
     */
    public function key(): int|string
    {
        return current($this->keys);
    }

    /**
     * Moves the iterator to the next position.
     */
    public function next(): void
    {
        $this->valid = (next($this->keys) !== false);
    }

    /**
     * Moves the iterator to the first position.
     */
    public function rewind(): void
    {
        $this->valid = (reset($this->keys) !== false);
    }

    /**
     * Is the current iterator position valid?
     */
    public function valid(): bool
    {
        return $this->valid;
    }
}
