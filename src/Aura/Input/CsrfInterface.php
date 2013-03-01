<?php
namespace Aura\Input;

interface CsrfInterface
{
    public function setField(Fieldset $fieldset);
    public function isValid(array $data);
}
