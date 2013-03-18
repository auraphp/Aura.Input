<?php
namespace Aura\Input;

interface AntiCsrfInterface
{
    public function setField(Fieldset $fieldset);
    public function isValid(array $data);
}
