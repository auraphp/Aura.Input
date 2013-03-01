<?php
namespace Aura\Input;

class MockFieldset extends Fieldset
{
    public function init()
    {
        // call parent for coverage
        parent::init();
        
        // now actually do something
        $this->setField('mock_field');
        
        // add a filter
        $this->filter->setRule('mock_field', 'Use alpha only!', function ($value) {
            return ctype_alpha($value);
        });
    }
}
