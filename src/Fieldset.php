<?php
declare(strict_types=1);
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

use ArrayObject;
use Aura\Filter_Interface\FilterInterface;
use Aura\Filter_Interface\FailureCollectionInterface;

/**
 *
 * A fieldset of inputs, where the inputs themselves may be values, other
 * fieldsets, or other collections.
 *
 * @package Aura.Input
 *
 */
class Fieldset extends AbstractInput
{
    /**
     *
     * A builder to create input objects.
     *
     * @var BuilderInterface
     *
     */
    protected $builder;

    /**
     *
     * A filter for the fieldset values.
     *
     * @var FilterInterface
     *
     */
    protected $filter;

    /**
     *
     * Inputs in the fieldset.
     *
     * @var array
     *
     */
    protected $inputs = [];

    /**
     *
     * Object for retaining information about options available to the form
     * inputs.
     *
     * @var mixed
     *
     */
    protected $options;

    /**
     *
     * Property for storing the result of the last filter() call.
     *
     * @var bool
     *
     */
    protected $success;

    /**
     *
     * Failures in the fieldset.
     *
     * @var FailureCollectionInterface
     *
     */
    protected $failures;

    /**
     *
     * Constructor.
     *
     * @param BuilderInterface $builder An object to build input objects.
     *
     * @param FilterInterface $filter A filter object for this fieldset.
     *
     * @param object $options An arbitrary options object for use when setting
     * up inputs and filters.
     *
     */
    public function __construct(
        BuilderInterface $builder,
        FilterInterface  $filter,
        $options = null
    ) {
        $this->builder  = $builder;
        $this->filter   = $filter;
        $this->options  = $options;
        $this->init();
    }

    /**
     *
     * Gets an input value from this fieldset.
     *
     * @param string $name The input name.
     *
     * @return mixed The input value.
     *
     */
    public function __get($name)
    {
        return $this->getInput($name)->read();
    }

    /**
     *
     * Sets an input value on this fieldset.
     *
     * @param string $name The input name.
     *
     * @param mixed $value The input value.
     *
     * @return void
     *
     */
    public function __set($name, $value)
    {
        $this->getInput($name)->fill($value);
    }

    /**
     *
     * Checks if a value is set on an input in this fieldset.
     *
     * @param string $name The input name.
     *
     * @return bool
     *
     */
    public function __isset($name)
    {
        if (! isset($this->inputs[$name])) {
            return false;
        }

        return $this->getInput($name)->read() !== null;
    }

    /**
     *
     * Sets the value of an input in this fieldset to null.
     *
     * @param string $name The input name.
     *
     * @return void
     *
     */
    public function __unset($name)
    {
        if (isset($this->inputs[$name])) {
            $this->getInput($name)->fill(null);
        }
    }

    /**
     *
     * Returns the filter object.
     *
     * @return FilterInterface
     *
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     *
     * Returns an individual input object by name.
     *
     * @param string $name The name of the input object.
     *
     * @return AbstractInput
     *
     */
    public function getInput($name)
    {
        if (! isset($this->inputs[$name])) {
            throw new Exception\NoSuchInput($name);
        }

        $input = $this->inputs[$name];
        $input->setNamePrefix($this->getFullName());
        return $input;
    }

    /**
     *
     * Returns the names of all input objects in this fieldset.
     *
     * @return array
     *
     */
    public function getInputNames()
    {
        return array_keys($this->inputs);
    }

    /**
     *
     * Returns the input builder.
     *
     * @return BuilderInterface
     *
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     *
     * Returns the options object
     *
     * @return mixed
     *
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * Fills this fieldset with input values.
     *
     * @param array $data The values for this fieldset.
     *
     * @return void
     *
     */
    public function fill(array $data)
    {
        $this->success = NULL;
        foreach ($this->inputs as $key => $input) {
            if (array_key_exists($key, $data)) {
                $input->fill($data[$key]);
            }
        }
    }

    /**
     *
     * Initializes the inputs and filter.
     *
     * @return void
     *
     */
    public function init()
    {
    }

    /**
     *
     * Did the input data pass the filter rules?
     *
     * @return null|bool
     *
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     *
     * Sets a new Field input.
     *
     * @param string $name The Field name.
     *
     * @param string $type A Field of this type; defaults to 'text'.
     *
     * @return Field
     *
     */
    public function setField($name, $type = null)
    {
        $this->inputs[$name] = $this->builder->newField($name, $type);
        return $this->inputs[$name];
    }

    /**
     *
     * Sets a new Fieldset input.
     *
     * @param string $name The Fieldset name.
     *
     * @param string $type A Fieldset of this type; defaults to $name.
     *
     * @return Fieldset
     *
     */
    public function setFieldset($name, $type = null)
    {
        $this->inputs[$name] = $this->builder->newFieldset($name, $type);
        return $this->inputs[$name];
    }

    /**
     *
     * Sets a new Collection input.
     *
     * @param string $name The Collection name.
     *
     * @param string $type A Collection of this type of Fieldset; defaults to
     * $name.
     *
     * @return Collection
     *
     */
    public function setCollection($name, $type = null)
    {
        $this->inputs[$name] = $this->builder->newCollection($name, $type);
        return $this->inputs[$name];
    }

    /**
     *
     * Returns an input in a format suitable for a view.
     *
     * @param string $name The input name.
     *
     * @return mixed
     *
     */
    public function get($name = null)
    {
        if ($name === null) {
            return $this;
        }

        $input = $this->getInput($name);
        return $input->get();
    }

    /**
     *
     * Filters the inputs on this fieldset.
     *
     * @return bool True if all the filter rules pass, false if not.
     *
     */
    public function filter()
    {
        $this->success = $this->filter->apply($this);
        $this->failures = $this->filter->getFailures();

        // Iterate on fieldset or collection and get failures
        foreach ($this->inputs as $name => $input) {
            if ($input instanceof Fieldset || $input instanceof Collection) {
                if (! $input->filter()) {
                    $this->success = false;
                    $failures = $input->getFailures();
                    if ($failures instanceof FailureCollectionInterface) {
                        $failures = $failures->getMessages();
                    }
                    $this->failures->addMessagesForField($name, $failures);
                }
            }
        }

        return $this->success;
    }

    /**
     *
     * Returns the failures.
     *
     * @return FailureCollectionInterface
     *
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     *
     * Returns the value of this input for use in arrays.
     *
     * @return array
     *
     */
    public function getValue()
    {
        $data = [];
        foreach ($this->inputs as $name => $input) {
            $data[$name] = $input->getValue();
        }
        return $data;
    }
}
