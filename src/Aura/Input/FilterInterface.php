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
 * A filter interface
 * 
 * @package Aura.Input
 * 
 */
interface FilterInterface
{
    /**
     * 
     * Add Rule on a field
     * 
     * @param string $field The field value
     * 
     * @param string $message The message when the rule fails
     * 
     * @param \Closure $closure A closure
     * 
     */
    public function addRule($field, $message, \Closure $closure);
    
    /**
     * 
     * Filter and Validate the data
     * 
     * @param mixed $values The value
     * 
     * @return bool
     * 
     */
    public function values(&$values);
    
    /**
     * 
     * Get messages
     * 
     * @param string $field All error messages or only for a single field
     * 
     * @return mixed
     * 
     */
    public function getMessages($field = null);
}
