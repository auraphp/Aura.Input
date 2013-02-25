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
 * A factory to create fields, fieldsets, and fieldset collections.
 * 
 * @package Aura.Form
 * 
 */
interface BuilderInterface
{
    public function newField($type, $name, $prefix);
    public function newFieldset($type, $name, $prefix);
    public function newCollection($type, $name, $prefix);
}
