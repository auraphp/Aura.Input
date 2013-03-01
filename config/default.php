<?php
/**
 * Loader
 */
$loader->add('Aura\Input\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Aura\Input\FieldCollection
 */
$di->params['Aura\Input\FieldCollection']['field_factory'] = $di->lazyNew('Aura\Input\FieldBuilder');


/**
 * Aura\Input\Form
 */
$di->params['Aura\Input\Form']['fields'] = $di->lazyNew('Aura\Input\FieldCollection');
$di->params['Aura\Input\Form']['options'] = $di->lazyNew('Aura\Input\Options');
$di->params['Aura\Input\Form']['filter'] = $di->lazyNew('Aura\Input\Filter');
