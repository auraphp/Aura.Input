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
 * A field object.
 * 
 * @package Aura.Input
 * 
 */
class Field
{
    /**
     * 
     * The field type.
     * 
     * @var string
     * 
     */
    protected $type;
    
    /**
     * 
     * HTML attributes for the field as key-value pairs. The key is the
     * attribute name and the value is the attribute value.
     * 
     * @var array
     * 
     */
    protected $attribs = [];
    
    /**
     * 
     * Options for the field as key-value pairs (typically for select and
     * radio elements). The key is the option value and the value is the
     * option label.  Nested options may be honored as the key being an
     * optgroup label and the array value as the options under that optgroup.
     * 
     * @var array
     * 
     */
    protected $options = [];
    
    /**
     * 
     * Constructor.
     * 
     * @param string $type The field type.
     * 
     */
    public function __construct($type)
    {
        $this->type = $type;
    }
    
    /**
     * 
     * Sets the HTML attributes on this field.
     * 
     * @param array $attribs HTML attributes for the field as key-value pairs;
     * the key is the attribute name and the value is the attribute value.
     * 
     * @return self
     * 
     */
    public function attribs(array $attribs)
    {
        $this->attribs = $attribs;
        return $this;
    }
    
    /**
     * 
     * Sets the value options for this field, typically for select and radios.
     * 
     * @param array $options Options for the field as key-value pairs. The key
     * is the option value and the value is the option label.  Nested options
     * may be honored as the key being an optgroup label and the array value
     * as the options under that optgroup.
     * 
     * @return self
     * 
     */
    public function options(array $options)
    {
        $this->options = $options;
        return $this;
    }
    
    /**
     * 
     * Returns this field as a plain old PHP array.
     * 
     * @return array An array with keys `'type'`, `'attribs'`, and 
     * `'options'`.
     * 
     */
    public function asArray()
    {
        return [
            'type'    => $this->type,
            'attribs' => $this->attribs,
            'options' => $this->options,
        ];
    }
}
