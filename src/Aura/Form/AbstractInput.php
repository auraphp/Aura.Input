<?php
namespace Aura\Form;

abstract class AbstractInput implements InputInterface
{
    protected $name;
    
    // the FULL NAME to be presented in the form, as vs
    // the simple property name in the fieldset.
    public function setName($name, $prefix = null)
    {
        if ($prefix) {
            $name = $prefix . "[{$name}]";
        }
        $this->name = $name;
        return $this;
    }
}
