<?php
/**
 * Copyright © 2018 by Wood Street, Inc. All Rights reserved.
 */

namespace Aura\Input;


class ConfigurableForm extends Form
{

    public function init(): void
    {
        parent::init(); // for code coverage

        $this->setField('foo')->setOptions((array) $this->options);
    }

}