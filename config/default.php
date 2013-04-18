<?php
/**
 * Loader
 */
$loader->add('Aura\Input\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Aura\Input\Fieldset
 */
$di->params['Aura\Input\Fieldset']['builder'] = $di->lazyNew('Aura\Input\Builder');
$di->params['Aura\Input\Fieldset']['filter'] = $di->lazyNew('Aura\Input\Filter');
