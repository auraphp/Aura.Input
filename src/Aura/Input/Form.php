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
 * A form object of fields and values.
 * 
 * @package Aura.Input
 * 
 */
class Form
{
    /**
     * 
     * Fields in the form.
     * 
     * @var FieldCollection
     * 
     */
    protected $fields;
    
    /**
     * 
     * Values of the fields.
     * 
     * @var array
     * 
     */
    protected $values = [];
    
    /**
     * 
     * Constructor; calls `init()` at the end.
     * 
     * @param FieldCollection $fields A field collection object.
     * 
     * @see init()
     * 
     */
    public function __construct(FieldCollection $fields)
    {
        $this->fields = $fields;
        $this->init();
    }
    
    /**
     * 
     * Initializes this form object; called at the end of `__construct()`.
     * 
     * @return void
     * 
     */
    protected function init()
    {
    }
    
    /**
     * 
     * Sets a field into the form.
     * 
     * @param string $name The field name; e.g., `'foo[bar]'`.
     * 
     * @param string $type The field type; e.g. `'checkbox'`.
     * 
     * @return Field
     * 
     */
    public function setField($name, $type = 'text')
    {
        return $this->fields->set($name, $type);
    }
    
    /**
     * 
     * Returns a single field as a plain old PHP array.
     * 
     * @param string $name The name of the field to get.
     * 
     * @return array An array with keys `'name'`, `'type'`, `'attribs'`,
     * `'options'`, and `'value'`.
     * 
     */
    public function getField($name)
    {
        $value = isset($this->values[$name])
               ? $this->values[$name]
               : null;
               
        return ['name' => $name]
             + $this->fields->get($name)->asArray()
             + ['value' => $value];
    }
    
    /**
     * 
     * Returns the field collection.
     * 
     * @return FieldCollection
     * 
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * 
     * Sets the values of fields. The value of `$data['foo']['bar']['baz']`
     * will be set on the field named `'foo[bar][baz]'`.
     * 
     * @param array $data The values to set into fields.
     * 
     * @return void
     * 
     * @see getValue()
     * 
     */
    public function setValues($data)
    {
        $names = $this->fields->getNames();
        foreach ($names as $name) {
            $value = $this->getValue($name, $data);
            if ($value !== null) {
                $this->values[$name] = $value;
            }
        }
    }
    
    /**
     * 
     * Gets the value for a field out of a data array.
     * 
     * @param string $name The field name.
     * 
     * @param string $data The data array, or a subset of an original array.
     * 
     * @return mixed
     * 
     * @see setValues()
     * 
     */
    protected function getValue($name, $data)
    {
        // if no subarray in the name, return the named key from the data
        $pos = strpos($name, '[');
        if ($pos === false) {
            return isset($data[$name]) ? $data[$name] : null;
        }
        
        // get the data key:
        // foo[bar][baz] => foo
        $key = substr($name, 0, $pos); // foo

        // get the subarray name:
        // foo[bar][baz] => bar[baz]
        $end = strpos($name, ']');
        $sub = substr($name, $pos + 1, $end - $pos - 1)
             . substr($name, $end + 1);
        $value = isset($data[$key]) ? $data[$key] : null;
        // recursively descend into the data
        return $this->getValue($sub, $value);
    }
    
    /**
     * 
     * Returns the value of all fields as an array keyed on the field names.
     * Fields named, e.g., `'foo[bar][baz]'` will be returned in the array
     * element `$values['foo']['bar']['baz']`.
     * 
     * @return array
     * 
     * @see setValue()
     * 
     */
    public function getValues()
    {
        $data = [];
        foreach ($this->values as $name => $value) {
            $this->setValue($name, $value, $data);
        }
        return $data;
    }
    
    /**
     * 
     * Sets the value of a field into a data array.
     * 
     * @param string $name The field name, or part of a field name.
     * 
     * @param string $value The value to set into the data array.
     * 
     * @param string &$data A reference to the data array.
     * 
     * @return void
     * 
     * @see getValues()
     * 
     */
    protected function setValue($name, $value, &$data)
    {
        // if no subarray in the name, set the named value into the data
        $pos = strpos($name, '[');
        if ($pos === false) {
            $data[$name] = $value;
            return;
        }
            
        // create a subarray in the data
        // foo[bar][baz] => foo
        $key = substr($name, 0, $pos); // foo
        if (! isset($data[$key])) {
            $data[$key] = [];
        }
        
        // get the subarray name:
        // foo[bar][baz] => bar[baz]
        $end = strpos($name, ']');
        $sub = substr($name, $pos + 1, $end - $pos - 1)
             . substr($name, $end + 1);
    
        // recursively descend into the data
        $this->setValue($sub, $value, $data[$key]);
    }
}
