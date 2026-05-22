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

use Aura\Filter_Interface\FailureCollectionInterface;
use Aura\Filter_Interface\FilterInterface;
use Aura\Filter_Interface\FilterResult;
use Aura\Filter_Interface\FilterResultInterface;
use Aura\Input\Filter\FailureCollection;

/**
 *
 * A closure-based filter implementation for Aura.Input.
 *
 * Rules are registered as closures with the signature:
 *   function ($value, $fields): bool
 * where $fields is the Fieldset object (passed by handle).
 *
 * @package Aura.Input
 *
 */
class Filter implements FilterInterface
{
    /**
     * The array of rules to be applied to fields.
     *
     * @var array<string, array<int, array{string, \Closure}>>
     */
    protected array $rules = [];

    /**
     * Live failures during / after the current apply() run.
     * Kept as instance state so closures can call
     * $filter->getFailures()->addMessagesForField() mid-run.
     */
    protected FailureCollection $failures;

    /**
     * A prototype FailureCollection (cloned on each apply() call).
     */
    protected FailureCollection $proto_failures;

    /**
     * Initialize filters.
     */
    public function __construct(?FailureCollectionInterface $failures = null)
    {
        $proto = ($failures instanceof FailureCollection)
            ? $failures
            : new FailureCollection();

        $this->proto_failures = $proto;
        $this->failures       = clone $proto;
        $this->init();
    }

    /**
     * Hook for subclasses.
     */
    protected function init(): void
    {
    }

    /**
     * Resets all previous rules for a field and adds a single rule.
     */
    public function setRule(string $field, string $message, \Closure $closure): void
    {
        unset($this->rules[$field]);
        $this->addRule($field, $message, $closure);
    }

    /**
     * Adds a rule to a field (multiple rules per field are supported).
     */
    public function addRule(string $field, string $message, \Closure $closure): void
    {
        $this->rules[$field][] = [$message, $closure];
    }

    /**
     * Applies all rules to $values (a Fieldset object).
     *
     * Never mutates the caller's variable — but for Fieldset subjects the
     * Fieldset IS the container; closures write back via $fields->name = ...
     * through the object handle.
     *
     * Keeps $this->failures as live state during the run so mid-run closures
     * that call getFailures()->addMessagesForField() continue to work.
     */
    public function apply(array|object $values): FilterResultInterface
    {
        $this->failures = clone $this->proto_failures;

        foreach ($this->rules as $field => $rules) {
            foreach ($rules as [$message, $closure]) {
                $passed = $closure($values->$field, $values);

                if (! $passed) {
                    $this->failures->addMessagesForField($field, $message);
                }
            }
        }

        return new FilterResult(
            $this->failures->isEmpty(),
            $values,
            $this->failures
        );
    }

    /**
     * Returns the failures from the most recent apply() call.
     * Not on FilterInterface — concrete helper for closures and Fieldset.
     */
    public function getFailures(): FailureCollectionInterface
    {
        return $this->failures;
    }

    /**
     * Returns all messages, or messages for a single field.
     *
     * @return array<string, string[]>|string[]
     */
    public function getMessages(?string $field = null): array
    {
        if ($field === null) {
            return $this->failures->getMessages();
        }

        return $this->failures->getMessagesForField($field);
    }

    /**
     * Manually adds messages to a particular field.
     *
     * @param string|string[] $messages
     */
    public function addMessages(string $field, string|array $messages): void
    {
        $this->failures->addMessagesForField($field, $messages);
    }
}
