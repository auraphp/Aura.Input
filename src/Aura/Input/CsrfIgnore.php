<?php
namespace Aura\Input;

class CsrfIgnore implements CsrfInterface
{
    public function setField(Fieldset $fieldset)
    {
        return;
    }
    
    public function isValid(array $data)
    {
        return true;
    }
}
