<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Input
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Input;

use ArrayAccess;
use Countable;
use IteratorAggregate;

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
     * 
     * Factory to create a particular fieldset type.
     * 
     * @var callable
     * 
     */
    protected $factory;
    
    /**
     * 
     * Fieldsets in the collection.
     * 
     * @var array
     * 
     */
    protected $fieldsets = [];

    /**
     * 
     * Constructor.
     * 
     * @param callable $factory A factory to create the fieldset objects for
     * this collection.
     * 
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * 
     * Support for this input when addressed via Fieldset::__set().
     * 
     * @param array $data The data for each fieldset in the collection.
     * 
     */
    public function fill(array $data)
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
     * 
     * Applies each fieldset filter.
     * 
     * @return bool True if all filters passed, false if one or more failed.
     * 
     */
    public function filter()
    {
        $passed = true;
        foreach ($this->fieldsets as $key => $fieldset) {
            if (! $fieldset->filter()) {
                $passed = false;
            }
        }
        return $passed;
    }
    
    /**
     * 
     * Returns the messages for the fieldset filters.
     * 
     * @param mixed $key The fieldset key to return messages for; if null, 
     * returns messages from all fieldsets.
     * 
     * @return array
     * 
     */
    public function getMessages($key = null)
    {
        if ($key !== null) {
            return $this->fieldsets[$key]->getMessages();
        }
        
        $messages = [];
        foreach ($this->fieldsets as $key => $fieldset) {
            $messages[$key] = $fieldset->getMessages();
        }
        return $messages;
    }
    
    /**
     * 
     * IteratorAggregate: returns an external iterator for this collection.
     * 
     * @return CollectionIterator
     * 
     */
    public function getIterator()
    {
        return new CollectionIterator($this);
    }

    /**
     * 
     * Gets all the keys for all Fieldsets in this collection.
     * 
     * @return array
     * 
     */
    public function getKeys()
    {
        return array_keys($this->fieldsets);
    }
    
    /**
     * 
     * Creates and returns a new fieldset.
     * 
     * @param string $key The key for the new fieldset.
     * 
     * @return Fieldset
     * 
     */
    protected function newFieldset($key)
    {
        $factory = $this->factory;
        $fieldset = $factory();
        $fieldset->setName($key);
        return $fieldset;
    }
    
    /**
     * 
     * ArrayAccess: returns the fieldset at a particular offset.
     * 
     * @param mixed $offset The fieldset key.
     * 
     * @return Fieldset
     * 
     */
    public function offsetGet($offset)
    {
        $fieldset = $this->fieldsets[$offset];
        $fieldset->setNamePrefix($this->getFullName());
        return $fieldset;
    }
    
    /**
     * 
     * ArrayAccess: sets an offset as a Fieldset.
     * 
     * @param mixed $offset The Fieldset key.
     * 
     * @param Fieldset $fieldset The Fieldset for that key.
     * 
     * @return void
     * 
     */
    public function offsetSet($offset, $fieldset)
    {
        $this->fieldsets[$offset] = $fieldset;
    }
    
    /**
     * 
     * ArrayAccess: is a particular Fieldset key set?
     * 
     * @param mixed $offset The Fieldset key.
     * 
     * @return bool True if the Fielset key is set, false if not.
     * 
     */
    public function offsetExists($offset)
    {
        return isset($this->fieldsets[$offset]);
    }
    
    /**
     * 
     * ArrayAccess: unsets a particular Fieldset key.
     * 
     * @param mixed $offset The Fieldset key.
     * 
     * @return void
     * 
     */
    public function offsetUnset($offset)
    {
        unset($this->fieldsets[$offset]);
    }
    
    /**
     * 
     * Countable: returns the number of Fieldsets in this collection.
     * 
     * @return int
     * 
     */
    public function count()
    {
        return count($this->fieldsets);
    }
    
    /**
     * 
     * Returns the value of this input for use in arrays.
     * 
     * @return array
     * 
     */
    public function getValue()
    {
        $data = [];
        foreach ($this->fieldsets as $key => $fieldset) {
            $data[$key] = $fieldset->getValue();
        }
        return $data;
    }
}
