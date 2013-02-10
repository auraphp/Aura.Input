<?php
namespace Aura\Input;

class Filter implements FilterInterface
{
    protected $rules = [];
    
    protected $messages = [];
    
    public function setRule($field, $message, \Closure $closure)
    {
        $this->rules[$field] = [$message, $closure];
    }
    
    public function values(&$values)
    {
        // reset the messages
        $this->messages = [];
        
        // go through each of the rules
        foreach ($this->rules as $field => $rule) {
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
        
        // if there are messages, one or more values failed
        return $this->messages ? false : true;
    }
    
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
