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
 * A factory to create fields, fieldsets, and fieldset collections.
 * 
 * @package Aura.Input
 * 
 */
interface BuilderInterface
{
    public function newField($type, $name);
    public function newFieldset($type, $name);
    public function newCollection($type, $name);
}
