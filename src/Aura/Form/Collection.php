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

use ArrayObject;
use IteratorAggregate;

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
     * @var ArrayObject
     * 
     */
    protected $fieldsets;

    /**
     * 
     * Constructor.
     * 
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
        $this->fieldsets = new ArrayObject([]);
    }

    public function load($data)
    {
        foreach ((array) $data as $key => $values) {
            if (! $this->fieldsets->offsetExists($key)) {
                $this->fieldsets[$key] = $this->newFieldset($key);
            }
            $this->fieldsets[$key]->load($values);
        }
    }
    
    public function read()
    {
        return $this;
    }
    
    public function export()
    {
        return $this->fieldsets->getArrayCopy();
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
    
    public function getMessages($key = null)
    {
        if ($key) {
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
     * @return ArrayIterator
     * 
     */
    public function getIterator()
    {
        return $this->fieldsets->getIterator();
    }

    protected function newFieldset($name)
    {
        $factory = $this->factory;
        $fieldset = $factory();
        $fieldset->setName($name, $this->name);
        $fieldset->prep();
        return $fieldset;
    }
}
