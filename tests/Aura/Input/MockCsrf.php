<?php
namespace Aura\Input;

class MockCsrf extends CsrfIgnore
{
    protected $value = 'goodvalue';
    
    protected $name = '__csrf_token';
    
    public function isValid(array $data)
    {
        // call parent for coverage
        parent::isValid($data);
        // ignore in favor of this
        return isset($data[$this->name])
            && $data[$this->name] == $this->value;
    }
    
    public function setField(Fieldset $fieldset)
    {
        // call parent for coverage
        parent::setField($fieldset);
        // ignore in favor of this
        $fieldset->setField($this->name, 'hidden')
                 ->setValue($this->value);
    }
}
