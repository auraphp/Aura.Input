<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Input
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Input;

/**
 * 
 * Defines the interface for anti-CSRF protection logic.
 * 
 * @package Aura.Input
 * 
 */
interface AntiCsrfInterface
{
    /**
     * 
     * Sets the field value for the anti-CSRF value.
     * 
     * @param Fieldset $fieldset The fieldset on which to set the anti-CSRF
     * value.
     * 
     * @return void
     * 
     */
    public function setField(Fieldset $fieldset);
    
    /**
     * 
     * Checks the raw user input to see if the incoming anti-CSRF value is
     * valid.
     * 
     * @param array $data The incoming user data, generally from $_POST.
     * 
     * @return bool True if the incoming data has the correct anti-CSRF value,
     * false if not.
     * 
     */
    public function isValid(array $data);
}
