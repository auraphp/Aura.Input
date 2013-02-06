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
 * A factory to create field objects.
 * 
 * @package Aura.Input
 * 
 */
class FieldFactory
{
    /**
     * 
     * Creates a new field object.
     * 
     * @param string $type $the field type.
     * 
     * @return Field
     * 
     */
    public function newInstance($type)
    {
        return new Field($type);
    }
}
