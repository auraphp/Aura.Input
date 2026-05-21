<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/MIT-license.php MIT
 *
 */
namespace Aura\Input\Filter;

use Aura\Filter_Interface\FailureCollectionInterface;
use Aura\Filter_Interface\FailureInterface;

class FailureCollection implements FailureCollectionInterface
{
    /**
     *
     * Array of failed messages for fields.
     *
     * @var array
     *
     */
    protected $messages = [];

    /**
     *
     * Is the failure collection empty?
     *
     */
    public function isEmpty(): bool
    {
        return count($this->messages) === 0;
    }

    /**
     *
     * Adds an additional failure on a field.
     *
     * @param string $field The field that failed.
     *
     * @param string|array $messages The failure messages.
     *
     */
    public function addMessagesForField($field, $messages)
    {
        if (! isset($this->messages[$field])) {
            $this->messages[$field] = [];
        }

        $this->messages[$field] = array_merge(
            $this->messages[$field],
            (array) $messages
        );
    }

    /**
     *
     * Adds a single failure message for a field.
     *
     */
    public function add(string $field, string $message, array $args = []): FailureInterface
    {
        $this->addMessagesForField($field, $message);
        return new Failure($field, $message, $args);
    }

    /**
     *
     * Sets a single failure message for a field, replacing any previous ones.
     *
     */
    public function set(string $field, string $message, array $args = []): FailureInterface
    {
        $this->messages[$field] = [];
        return $this->add($field, $message, $args);
    }

    /**
     *
     * Returns all failure messages for all fields.
     *
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     *
     * Returns all failure messages for one field.
     *
     */
    public function getMessagesForField(string $field): array
    {
        if (! isset($this->messages[$field])) {
            return [];
        }

        return $this->messages[$field];
    }
}
