<?php
namespace Aura\Input;

interface FilterInterface
{
    public function setRule($field, $message, \Closure $closure);
    public function values(&$values);
    public function getMessages($field = null);
}
