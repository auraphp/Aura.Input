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
     * Sets a filter rule on a field.
     *
     * @param string $field The field name.
     *
     * @param string $message The message when the rule fails.
     *
     * @param \Closure $closure A closure that implements the rule. It must
     * have the signature `function ($value, &$fields)`; it must return
     * boolean true on success, or boolean false on failure.
     *
     */
    public function setRule($field, $message, \Closure $closure);

    /**
     * 
     * Filter (sanitize and validate) the data.
     * 
     * @param mixed $values The values to be filtered.
     * 
     * @return bool True if all rules passed; false if one or more failed.
     * 
     */
    public function values(&$values);
    
    /**
     * 
     * Gets the messages for all fields, or for a single field.
     * 
     * @param string $field If empty, return all messages for all fields;
     * otherwise, return only messages for the named field.
     * 
     * @return mixed
     * 
     */
    public function getMessages($field = null);
    
    /**
     * 
     * Manually add messages to a particular field.
     * 
     * @param string $field Add to this field.
     * 
     * @param string|array $messages Add these messages to the field.
     * 
     * @return void
     * 
     */
    public function addMessages($field, $messages);
}
