<?php
namespace Aura\Input;

class MockFieldset extends Fieldset
{
    public function init(): void
    {
        // call parent for coverage
        parent::init();
        
        // now actually do something
        $this->setField('foo');
        $this->setField('bar');
        $this->setField('baz');
        
        // add a filter
        $this->filter->addRule('foo', 'Use alpha only!', function ($value) {
            return ctype_alpha($value);
        });
    }
}
