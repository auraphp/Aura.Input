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

/**
 * 
 * A factory to create Field, Fieldset, and fieldset Collection objects.
 * 
 * @package Aura.Form
 * 
 */
class Builder implements BuilderInterface
{
    protected $collection_class = 'Aura\Form\Collection';
    
    protected $field_class = 'Aura\Form\Field';
    
    protected $map;
    
    /**
     * 
     * A map of Fieldset types to *closures that create objects* (as
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
    public function newField($type, $name, $prefix)
    {
        $class = $this->field_class;
        $field = new $class($type);
        $field->setName($name, $prefix);
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
    public function newFieldset($type, $name, $prefix)
    {
        $factory = $this->map[$type];
        $fieldset = $factory();
        $fieldset->setName($name, $prefix);
        $fieldset->prep();
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
    public function newCollection($type, $name, $prefix)
    {
        $class = $this->collection_class;
        $collection = new $class($this->map[$type]);
        $collection->setName($name, $prefix);
        return $collection;
    }
}
