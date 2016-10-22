<?php
namespace Aura\Input\Example;

use Aura\Input\Filter;

class ContactFilter extends Filter
{
    protected function init()
    {
        $this->setRule(
            'first_name',
            'First name must be alphabetic only.',
            function ($value) {
                return ctype_alpha($value);
            }
        );

        $this->setRule(
            'last_name',
            'Last name must be alphabetic only.',
            function ($value) {
                return ctype_alpha($value);
            }
        );

        $this->setRule(
            'email',
            'Email not valid.',
            function ($value) {
                return filter_var($value, FILTER_VALIDATE_EMAIL);
            }
        );
    }
}
