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

/**
 * 
 * A factory to create Field, Fieldset, and fieldset Collection objects.
 * 
 * @package Aura.Input
 * 
 */
class Builder implements BuilderInterface
{
    /**
     * 
     * The class to use for creating fieldset collections.
     * 
     * @var string
     * 
     */
    protected $collection_class = 'Aura\Input\Collection';
    
    /**
     * 
     * The class to use for creating fields.
     * 
     * @var string
     * 
     */
    protected $field_class = 'Aura\Input\Field';
    
    /**
     * 
     * A map of fieldset types to *callables that create objects* (as
     * vs class names).
     * 
     * @var array
     * 
     */
    protected $fieldset_map;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $map A map of fieldset types to *callables that create 
     * objects* (as vs class names).
     * 
     */
    public function __construct(array $fieldset_map = [])
    {
        $this->fieldset_map = $fieldset_map;
    }
    
    /**
     * 
     * Creates a new Field object.
     * 
     * @param string $type The field type.
     * 
     * @param string $name The field name.
     * 
     * @return Field
     * 
     */
    public function newField($type, $name)
    {
        $class = $this->field_class;
        $field = new $class($type);
        $field->setName($name);
        return $field;
    }
    
    /**
     * 
     * Creates a new Fieldset object.
     * 
     * @param string $type The fieldset type.
     * 
     * @param string $name The fieldset name.
     * 
     * @return Fieldset
     * 
     */
    public function newFieldset($type, $name)
    {
        $factory = $this->fieldset_map[$type];
        $fieldset = $factory();
        $fieldset->setName($name);
        return $fieldset;
    }
    
    /**
     * 
     * Creates a new Collection object.
     * 
     * @param string $type The collection type.
     * 
     * @param string $name The collection name.
     * 
     * @return Collection
     * 
     */
    public function newCollection($type, $name)
    {
        $class = $this->collection_class;
        $collection = new $class($this->fieldset_map[$type]);
        $collection->setName($name);
        return $collection;
    }
}
