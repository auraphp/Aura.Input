<?php
namespace Aura\Input;

class Form extends Fieldset
{
    protected $csrf;
    
    public function setCsrf(CsrfInterface $csrf)
    {
        $this->csrf = $csrf;
        $this->csrf->setField($this);
    }
    
    public function getCsrf()
    {
        return $this->csrf;
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
        if ($this->csrf && ! $this->csrf->isValid($data)) {
            throw new Exception\CsrfViolation;
        }
        parent::fill($data);
    }
}
