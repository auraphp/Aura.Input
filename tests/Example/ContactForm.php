<?php
namespace Aura\Input\Example;

use Aura\Input\Form;

class ContactForm extends Form
{
    public function init()
    {
        // basic info
        $this->setField('first_name');
        $this->setField('last_name');
        $this->setField('email');
        $this->setField('website');
        // one address
        $this->setFieldset('address');
        // many phone numbers
        $this->setCollection('phone_numbers', 'phone');
    }
}
