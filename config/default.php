<?php
/**
 * Loader
 */
$loader->add('Aura\Form\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Aura\Form\FieldCollection
 */
$di->params['Aura\Form\FieldCollection']['field_factory'] = $di->lazyNew('Aura\Form\FieldBuilder');


/**
 * Aura\Form\Form
 */
$di->params['Aura\Form\Form']['fields'] = $di->lazyNew('Aura\Form\FieldCollection');
$di->params['Aura\Form\Form']['options'] = $di->lazyNew('Aura\Form\Options');
$di->params['Aura\Form\Form']['filter'] = $di->lazyNew('Aura\Form\Filter');
