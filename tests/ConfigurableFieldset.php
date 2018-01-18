<?php
/**
 * Copyright © 2018 by Wood Street, Inc. All Rights reserved.
 */

namespace Aura\Input;

class ConfigurableFieldset extends Fieldset
{
    public function init()
    {
        // call parent for coverage
        parent::init();

        // pass configuration options to a child field
        $this->setField('foo', 'select')->setOptions((array) $this->options);
    }
}
