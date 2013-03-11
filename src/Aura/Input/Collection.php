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

use IteratorAggregate;

/**
 * 
 * Represents a collection of fieldsets of a single type.
 * 
 * @package Aura.Input
 * 
 */
class Collection extends AbstractInput implements IteratorAggregate
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
        return new CollectionIterator($this->fieldsets, $this->getFullName());
    }

    protected function newFieldset($name)
    {
        $factory = $this->factory;
        $fieldset = $factory();
        $fieldset->setName($name);
        return $fieldset;
    }
}
