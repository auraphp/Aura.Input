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
 * A single field in a fieldset.
 * 
 * @package Aura.Input
 * 
 */
class Field extends AbstractInput
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
     * Options for the field as key-value pairs (typically for checkbox, select,
     * and radio elements). The key is the option value and the value is the
     * option label.  Nested options may be honored as the key being an
     * optgroup label and the array value as the options under that optgroup.
     * 
     * @var array
     * 
     */
    protected $options = [];
    
    /**
     * 
     * The value for the field.  This may or may not be the same as the
     * 'value' attribue.
     * 
     * @var mixed
     * 
     */
    protected $value;
    
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
    
    public function load($value)
    {
        $this->setValue($value);
        return true;
    }
    
    public function read()
    {
        return $this->value;
    }
    
    /**
     * 
     * Returns this field as a plain old PHP array.
     * 
     * @return array An array with keys `'type'`, `'name'`, `'attribs'`, 
     * `'options'`, and `'value'`.
     * 
     */
    public function export()
    {
        $attribs = array_merge(
            [
                // force a particular order on some attributes
                'id'   => null,
                'type' => null,
                'name' => null,
            ],
            $this->attribs
        );
        
        return [
            'type'          => $this->type,
            'name'          => $this->getFullName(),
            'attribs'       => $attribs,
            'options'       => $this->options,
            'value'         => $this->value,
        ];
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
    public function setAttribs(array $attribs)
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
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
    }
}
