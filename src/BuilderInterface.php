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
     * @param string $name The field name.
     * 
     * @param string $type The field type.
     * 
     * @return Field
     * 
     */
    public function newField($name, $type);

    /**
     *
     * Creates a new Fieldset object.
     *
     * @param string $name The fieldset name.
     *
     * @param string $type The fieldset type.
     *
     * @param mixed $options Optional: configuration options for the fieldset.
     *
     * @return Fieldset
     *
     */
    public function newFieldset($name, $type, $options = null);
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
    public function newCollection($name, $type);
}
