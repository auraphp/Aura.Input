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
 * The interface for a builder to create fields, fieldsets, and fieldset
 * collections.
 * 
 * @package Aura.Input
 * 
 */
interface BuilderInterface
{
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
    public function newField($type, $name);
    
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
    public function newFieldset($type, $name);
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
    public function newCollection($type, $name);
}
