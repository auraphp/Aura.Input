<?php
namespace Aura\Input\Example;

use Aura\Input\Fieldset;

class AddressFieldset extends Fieldset
{
    public function init(): void
    {
        $this->setField('street');
        $this->setField('city');
        $this->setField('state');
        $this->setField('zip');
    }
}
