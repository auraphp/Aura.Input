<?php
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
     *
     * The fieldsets over which we are iterating.
     *
     * @var array
     *
     */
    protected $fieldsets;

    /**
     *
     * The keys to iterate over in the fieldsets.
     *
     * @var array
     *
     */
    protected $keys;

    /**
     *
     * Is the current iterator position valid?
     *
     * @var bool
     *
     */
    protected $valid;

    /**
     *
     * Constructor.
     *
     * @param Collection $collection The fieldsets over which to iterate.
     *
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        $this->keys = $this->collection->getKeys();
    }

    /**
     *
     * Returns the value at the current iterator position.
     *
     * @return mixed
     *
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->collection->offsetGet($this->key());
    }

    /**
     *
     * Returns the current iterator position.
     *
     * @return mixed
     *
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return current($this->keys);
    }

    /**
     *
     * Moves the iterator to the next position.
     *
     * @return void
     *
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->valid = (next($this->keys) !== false);
    }

    /**
     *
     * Moves the iterator to the first position.
     *
     * @return void
     *
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->valid = (reset($this->keys) !== false);
    }

    /**
     *
     * Is the current iterator position valid?
     *
     * @return boolean
     *
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->valid;
    }
}
