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

use ArrayObject;
use Aura\Filter_Interface\FilterInterface;

/**
 *
 * A filter
 *
 * @package Aura.Input
 *
 */
class Filter implements FilterInterface
{
    /**
     *
     * The array of rules to be applied to fields.
     *
     * @var array
     *
     */
    protected $rules = [];

    /**
     *
     * The array of messages to be used when rules fail.
     *
     * @var array
     *
     */
    protected $messages = [];

    /**
     * Initialize filters
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     *
     * Does nothing
     *
     */
    protected function init()
    {
        # code...
    }

    /**
     *
     * Sets a filter rule on a field.
     *
     * @param string $field The field name.
     *
     * @param string $message The message when the rule fails.
     *
     * @param Closure $closure A closure that implements the rule. It must
     * have the signature `function ($value, &$fields)`; it must return
     * boolean true on success, or boolean false on failure.
     *
     */
    public function setRule($field, $message, \Closure $closure)
    {
        $this->rules[$field] = [$message, $closure];
    }

    /**
     *
     * Filter (sanitize and validate) the data.
     *
     * @param mixed $values The values to be filtered.
     *
     * @return bool True if all rules passed; false if one or more failed.
     *
     */
    public function apply(&$values)
    {
        // reset the messages
        $this->messages = [];

        // go through each of the rules
        foreach ($this->rules as $field => $rule) {
            // get the message and closure
            list($message, $closure) = $rule;

            // apply the closure to the data and get back the result
            $passed = $closure($values->$field, $values);

            // if the rule did not pass, retain a message for the field.
            // note that it is in an array, so that other implementations
            // can allow for multiple messages.
            if (! $passed) {
                if (! isset($this->messages[$field])) {
                    $this->messages[$field][] = $message;
                } else {
                    // the message should be first.
                    array_unshift($this->messages[$field], $message);
                }
            }
        }

        // if there are messages, one or more values failed
        return $this->messages ? false : true;
    }

    /**
     *
     * Gets the messages for all fields
     *
     * @return ArrayObject
     *
     */
    public function getFailures()
    {
        return new ArrayObject($this->messages);
    }

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
    public function addMessages($field, $messages)
    {
        if (! isset($this->messages[$field])) {
            $this->messages[$field][] = $messages;
        } else {
            $this->messages[$field] = array_merge(
                $this->messages[$field],
                (array) $messages
            );
        }
    }
}
