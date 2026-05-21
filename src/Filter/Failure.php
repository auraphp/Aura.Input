<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/MIT-license.php MIT
 *
 */
namespace Aura\Input\Filter;

use Aura\Filter_Interface\FailureInterface;

class Failure implements FailureInterface
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $args;

    public function __construct(string $field, string $message, array $args = [])
    {
        $this->field = $field;
        $this->message = $message;
        $this->args = $args;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function jsonSerialize(): array
    {
        return [
            'field' => $this->field,
            'message' => $this->message,
            'args' => $this->args,
        ];
    }
}
