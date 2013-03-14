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
 * A filter
 * 
 * @package Aura.Input
 * 
 */
class Filter implements FilterInterface
{
    protected $rules = [];
    
    protected $messages = [];
    
    /**
     * 
     * Set Rule on a field
     * 
     * @param string $field The field value
     * 
     * @param string $message The message when the rule fails
     * 
     * @param \Closure $closure A closure
     * 
     */
    public function setRule($field, $message, \Closure $closure)
    {
        $this->rules[$field][] = [$message, $closure];
    }
    
    /**
     * 
     * Filter and Validate the data
     * 
     * @param mixed $values The value
     * 
     * @return bool
     * 
     */
    public function values(&$values)
    {
        // reset the messages
        $this->messages = [];
        
        // go through each of the rules
        foreach ($this->rules as $field => $rules) {
            // A field can have more rules
            foreach ($rules as $rule) {
                // get the closure and message
                list($message, $closure) = $rule;
                
                // apply the closure to the data and get back the result
                $passed = $closure($values[$field]);
                
                // if the rule did not pass, retain a message for the field.
                // note that it is in an array, so that other implementations
                // can allow for multiple messages.
                if (! $passed) {
                    $this->messages[$field][] = $message;
                }
            }
        }
        
        // if there are messages, one or more values failed
        return $this->messages ? false : true;
    }
    
    /**
     * 
     * Get messages
     * 
     * @param string $field All error messages or only for a single field
     * 
     * @return mixed
     * 
     */
    public function getMessages($field = null)
    {
        if (! $field) {
            return $this->messages;
        }
        
        if (isset($this->messages[$field])) {
            return $this->messages[$field];
        }
        
        return [];
    }
}
