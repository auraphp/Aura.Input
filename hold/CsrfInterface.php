<?php
namespace Aura\Form;

interface CsrfInterface
{
    public function setField(Fieldset $fieldset);
    public function isValid(array $data);
}
