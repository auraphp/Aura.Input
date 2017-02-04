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

use Aura\Filter_Interface\FailureCollectionInterface;

class FailureCollection  implements FailureCollectionInterface
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
     * @return bool
     *
     */
    public function isEmpty()
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
     * @return null
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
     * Returns all failure messages for all fields.
     *
     * @return array
     *
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     *
     * Returns all failure messages for one field.
     *
     * @param string $field The field name.
     *
     * @return array
     *
     */
    public function getMessagesForField($field)
    {
        if (! isset($this->messages[$field])) {
            return array();
        }

        return $this->messages[$field];
    }
}
