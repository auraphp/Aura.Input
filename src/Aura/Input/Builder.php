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
    protected $collection_class = 'Aura\Input\Collection';
    
    protected $field_class = 'Aura\Input\Field';
    
    protected $map;
    
    /**
     * 
     * A map of Fieldset types to *callables that create objects* (as
     * vs class names).
     * 
     * @var array
     * 
     */
    public function __construct(
        array $map = []
    ) {
        $this->map = $map;
    }
    
    /**
     * 
     * Creates a new Field object.
     * 
     * @param string $name The field name.
     * 
     * @param string $type The field type.
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
     * @param string $name The fieldset name.
     * 
     * @param string $type The fieldset type.
     * 
     * @return Fieldset
     * 
     */
    public function newFieldset($type, $name)
    {
        $factory = $this->map[$type];
        $fieldset = $factory();
        $fieldset->setName($name);
        return $fieldset;
    }
    
    /**
     * 
     * Creates a new Collection object.
     * 
     * @param string $name The collection name.
     * 
     * @param string $type The collection type.
     * 
     * @return Collection
     * 
     */
    public function newCollection($type, $name)
    {
        $class = $this->collection_class;
        $collection = new $class($this->map[$type]);
        $collection->setName($name);
        return $collection;
    }
}
