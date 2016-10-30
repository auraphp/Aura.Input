<?php
namespace Aura\Input\Example;

use Aura\Input\Filter;

class ContactFilter extends Filter
{
    protected function init()
    {
        $this->addRule(
            'first_name',
            'First name must be alphabetic only.',
            function ($value) {
                return ctype_alpha($value);
            }
        );

        $this->addRule(
            'last_name',
            'Last name must be alphabetic only.',
            function ($value) {
                return ctype_alpha($value);
            }
        );

        $this->addRule(
            'email',
            'Email not valid.',
            function ($value) {
                return filter_var($value, FILTER_VALIDATE_EMAIL);
            }
        );
    }
}
