<?php
namespace Aura\Input\_Config;

use Aura\Di\Config;
use Aura\Di\Container;

class Common extends Config
{
    public function define(Container $di)
    {
        /**
         * Services
         */
        $di->set('input_form_factory', $di->lazyNew('Aura\Input\FormFactory'));

        /**
         * Aura\Input\Fieldset
         */
        $di->params['Aura\Input\Fieldset']['builder'] = $di->lazyNew('Aura\Input\Builder');
        $di->params['Aura\Input\Fieldset']['filter'] = $di->lazyNew('Aura\Input\Filter');
    }
    
    public function modify(Container $di)
    {
    }
}
