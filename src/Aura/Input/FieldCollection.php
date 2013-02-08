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

use ArrayIterator;
use IteratorAggregate;

/**
 * 
 * A collection of fields.
 * 
 * @package Aura.Input
 * 
 */
class FieldCollection implements IteratorAggregate
{
    /**
     * 
     * A factory to create field objects.
     * 
     * @var FieldFactory
     * 
     */
    protected $field_factory;
    
    /**
     * 
     * The fields in this collection.
     * 
     * @var FieldFactory
     * 
     */
    protected $fields = [];
    
    /**
     * 
     * Constructor.
     * 
     * @param FieldFactory $field_factory A factory for creating field.
     * 
     */
    public function __construct(FieldFactory $field_factory)
    {
        $this->field_factory = $field_factory;
    }
    
    /**
     * 
     * Sets a new field into the collection.
     * 
     * @param string $name The field name.
     * 
     * @param string $type The field type.
     * 
     * @return Field
     * 
     */
    public function set($name, $type)
    {
        $field = $this->field_factory->newInstance($type);
        $this->fields[$name] = $field;
        return $field;
    }
    
    /**
     * 
     * Gets a field in the collection.
     * 
     * @param string $name The name of the field to get.
     * 
     * @return Field
     * 
     */
    public function get($name)
    {
        return $this->fields[$name];
    }
    
    /**
     * 
     * Gets the names of all fields in this collection.
     * 
     * @return array
     * 
     */
    public function getNames()
    {
        return array_keys($this->fields);
    }
    
    public function getIterator()
    {
        return new ArrayIterator($this->fields);
    }
}
