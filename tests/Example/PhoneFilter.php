<?php
namespace Aura\Input\Example;

use Aura\Input\Filter;

class PhoneFilter extends Filter
{
    protected function init()
    {
        $this->addRule(
            'type',
            'Type is not valid.',
            function ($value) {
                $phoneTypes = [
                    'mobile',
                    'home',
                    'fax'
                ];
                return in_array($value, $phoneTypes);
            }
        );

        $this->addRule(
            'number',
            'Not a valid phone number.',
            function ($value) {
                return preg_match('/^[0-9-]+$/', $value);
            }
        );
    }
}
