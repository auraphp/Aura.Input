<?php
namespace Aura\Input;

class MockAntiCsrf implements AntiCsrfInterface
{
    protected $value = 'goodvalue';
    
    protected $name = '__csrf_token';
    
    public function setField(Fieldset $fieldset)
    {
        $fieldset->setField($this->name, 'hidden')
                 ->setValue($this->value);
    }
    
    public function isValid(array $data)
    {
        return isset($data[$this->name])
            && $data[$this->name] == $this->value;
    }
}
