<?php
namespace Aura\Form;

class MockFieldset extends Fieldset
{
    public function prep()
    {
        // call parent for coverage
        parent::prep();
        
        // now actually do something
        $this->setField('mock_field');
        
        // add a filter
        $this->filter->setRule('mock_field', 'Use alpha only!', function ($value) {
            return ctype_alpha($value);
        });
    }
}
