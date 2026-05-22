<?php
declare(strict_types=1);

/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/MIT-license.php MIT
 *
 */
namespace Aura\Input\Filter;

use Aura\Filter_Interface\FailureInterface;

/**
 *
 * Represents the failure of a closure-based filter rule.
 *
 * @package Aura.Input
 *
 */
class Failure implements FailureInterface
{
    /**
     * Constructor.
     *
     * @param string  $field   The name of the field that failed.
     * @param string  $message The failure message.
     * @param mixed[] $args    Arguments passed to the rule (empty for closure-based rules).
     */
    public function __construct(
        private readonly string $field,
        private readonly string $message,
        private readonly array $args = [],
    ) {
    }

    /**
     * Returns the name of the field that failed.
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Returns the failure message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Returns the arguments passed to the rule specification.
     *
     * @return mixed[]
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Returns a JSON-serializable representation of this failure.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'field'   => $this->field,
            'message' => $this->message,
            'args'    => $this->args,
        ];
    }
}
