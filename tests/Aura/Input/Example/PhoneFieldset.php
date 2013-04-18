<?php
namespace Aura\Input\Example;

use Aura\Input\Fieldset;

class PhoneFieldset extends Fieldset
{
    public function init()
    {
        $this->setField('type');
        $this->setField('number');
    }
}
