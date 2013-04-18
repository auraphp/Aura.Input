<?php
namespace Aura\Input;

class Form extends Fieldset
{
    protected $anti_csrf;
    
    public function setAntiCsrf(AntiCsrfInterface $anti_csrf)
    {
        $this->anti_csrf = $anti_csrf;
        $this->anti_csrf->setField($this);
    }
    
    public function getAntiCsrf()
    {
        return $this->anti_csrf;
    }
    
    /**
     * 
     * Fills this form with input values.
     * 
     * @param array $data The values for this fieldset.
     * 
     * @return void
     * 
     */
    public function fill(array $data)
    {
        if ($this->anti_csrf && ! $this->anti_csrf->isValid($data)) {
            throw new Exception\CsrfViolation;
        }
        parent::fill($data);
    }
    
    /**
     * 
     * Returns all the fields collection
     * 
     * @return \ArrayIterator
     * 
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }
}
