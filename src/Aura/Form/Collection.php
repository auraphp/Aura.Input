<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Form
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Form;

use IteratorAggregate;
use ArrayIterator;

/**
 * 
 * Represents a collection of fieldsets.
 * 
 * @package Aura.Form
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
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    public function load($data)
    {
        foreach ((array) $data as $key => $val) {
            if (! array_key_exists($key, $this->fieldsets)) {
                $this->fieldsets[$key] = $this->newFieldset($key);
            }
            $this->fieldsets[$key]->load($data);
        }
    }
    
    public function read()
    {
        return $this;
    }
    
    public function export()
    {
        return $this->fieldsets;
    }
    
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
     * IteratorAggregate: returns an external iterator for this struct.
     * 
     * @return ArrayIterator
     * 
     */
    public function getIterator()
    {
        return new ArrayIterator($this->fieldsets);
    }

    protected function newFieldset($name)
    {
        $factory = $this->factory;
        $fieldset = $factory();
        $fieldset->setName($name, $this->name);
        return $fieldset;
    }
}
