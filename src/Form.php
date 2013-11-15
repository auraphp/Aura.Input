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
 * A top-level form object to contain fields, fieldsets, collections, and
 * anti-CSRF protection.
 * 
 * @package Aura.Input
 * 
 */
class Form extends Fieldset
{
    /**
     * 
     * The anti-CSRF implementation, if any.
     * 
     * @var AntiCsrfInterface
     * 
     */
    protected $anti_csrf;
    
    /**
     * 
     * Sets the anti-CSRF implementation; calls the `setField()` method on the
     * implementation to set the anti-CSRF field.
     * 
     * @param AntiCsrfInterface $anti_csrf The anti-CSRF implementation.
     * 
     * @return void
     * 
     */
    public function setAntiCsrf(AntiCsrfInterface $anti_csrf)
    {
        $this->anti_csrf = $anti_csrf;
        $this->anti_csrf->setField($this);
    }
    
    /**
     * 
     * Returns the anti-CSRF implementation, if any.
     * 
     * @return mixed
     * 
     */
    public function getAntiCsrf()
    {
        return $this->anti_csrf;
    }
    
    /**
     * 
     * Fills this form with input values.
     * 
     * If an anti-CSRF implementation is set and the incoming data does not
     * have a valid anti-CSRF value, it will throw an exception.
     * 
     * @param array $data The values for this fieldset.
     * 
     * @return void
     * 
     * @throws Exception\CsrfViolation if an anti-CSRF implementation is set
     * and the incoming data does not have a valid anti-CSRF value.
     * 
     */
    public function fill(array $data)
    {
        if ($this->anti_csrf && ! $this->anti_csrf->isValid($data)) {
            throw new Exception\CsrfViolation;
        }
        parent::fill($data);
    }
    
    /**
     * 
     * Returns all the fields collection
     * 
     * @return \ArrayIterator
     * 
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->inputs);
    }
}
