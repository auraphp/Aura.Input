<?php
namespace Aura\Input\Example;

use Aura\Input\Fieldset;

class AddressFieldset extends Fieldset
{
    public function init()
    {
        $this->setField('street');
        $this->setField('city');
        $this->setField('state');
        $this->setField('zip');
    }
}
