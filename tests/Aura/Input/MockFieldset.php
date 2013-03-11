<?php
namespace Aura\Input;

class MockFieldset extends Fieldset
{
    public function init()
    {
        // call parent for coverage
        parent::init();
        
        // now actually do something
        $this->setField('foo');
        $this->setField('bar');
        $this->setField('baz');
        
        // add a filter
        $this->filter->setRule('foo', 'Use alpha only!', function ($value) {
            return ctype_alpha($value);
        });
    }
}
