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
    
    public function load($data)
    {
        if ($this->csrf && ! $this->csrf->isValid($data)) {
            return false;
        }
        return parent::load($data);
    }
}
