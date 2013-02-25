<?php
namespace Aura\Form;

interface InputInterface
{
    /**
     * 
     * Sets the name for the presentation layer; this may or may not be the
     * same as the field name proper.
     * 
     * @param string $name The field name proper.
     * 
     * @param string $prefix A prefix for the name, typically an array-style
     * prefix.
     * 
     * @return void
     * 
     */
    public function setName($name, $prefix = null);
    
    /**
     * 
     * Loads data value(s) into the input object.
     * 
     * @param mixed $data The data to set into the input object.
     * 
     * @return void
     * 
     */
    public function load($data);
    
    /**
     * 
     * Gets the current value(s) of the input object.
     * 
     * @return mixed The data from the input object, which in some cases may
     * be the input object itself.
     * 
     * @see Fieldset::__get()
     * 
     */
    public function read();
    
    /**
     * 
     * Returns the input object in a manner useful for the presentation layer,
     * generally an array.
     * 
     * @return mixed
     * 
     * @see Fieldset::get()
     * 
     */
    public function export();
}
