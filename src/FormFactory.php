<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Input
 * 
 * @license http://opensource.org/licenses/MIT-license.php MIT
 * 
 */
namespace Aura\Input;

use Aura\Input\Exception;

/**
 * 
 * A factory to create top-level form objects by name.
 * 
 * @package Aura.Input
 * 
 */
class FormFactory
{
    /**
     * 
     * A map of form names to factory callables.
     * 
     * @var array
     * 
     */
    protected $map = [];

    /**
     * 
     * Constructor.
     * 
     * @param array $map A map of form names to factory callables.
     * 
     */
    public function __construct($map = [])
    {
        $this->map = $map;
    }

    /**
     *
     * Returns a new instance of a named form.
     *
     * @param string $name The name of the form to create.
     *
     * @param mixed $options
     *
     * @return Form
     *
     * @throws Exception\NoSuchForm When the named form does not exist.
     */
    public function newInstance($name, $options = null)
    {
        if (! isset($this->map[$name])) {
            throw new Exception\NoSuchForm($name);
        }
        
        $factory = $this->map[$name];
        $form = $factory($options);
        return $form;
    }
}
