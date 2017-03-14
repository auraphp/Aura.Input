<?php
namespace Aura\Input\Example;

use Aura\Input\Filter;

class AddressFilter extends Filter
{
    protected function init()
    {
        $this->addRule(
            'street',
            'Street name must be present.',
            function ($value) {
                return ! empty($value);
            }
        );

        $this->addRule(
            'city',
            'City name must be present.',
            function ($value) {
                return ! empty($value);
            }
        );
    }
}
