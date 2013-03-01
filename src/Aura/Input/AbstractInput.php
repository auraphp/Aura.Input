<?php
namespace Aura\Input;

abstract class AbstractInput implements InputInterface
{
    protected $name;
    
    protected $array_name;
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function setArrayName($array_name)
    {
        $this->array_name = $array_name;
        return $this;
    }
    
    public function getFullName()
    {
        $name = $this->name;
        if ($this->array_name) {
            $name = $this->array_name . "[{$name}]";
        }
        return $name;
    }
}
