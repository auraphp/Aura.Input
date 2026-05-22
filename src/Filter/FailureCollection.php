<?php
declare(strict_types=1);

/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 */
namespace Aura\Input\Filter;

use Aura\Filter_Interface\FailureCollectionInterface;
use Aura\Filter_Interface\FailureInterface;

/**
 *
 * A failure collection for the closure-based Input filter.
 *
 * Stores plain string messages internally (no args tracking, matching the
 * closure-based filter's simpler contract). Implements the full
 * FailureCollectionInterface so it satisfies both the write (add/set) and
 * read (forField/forPath/getMessages/getNestedMessages) sides.
 *
 * @package Aura.Input
 *
 */
class FailureCollection implements FailureCollectionInterface
{
    /**
     * Messages keyed by field name → string[].
     *
     * @var array<string, string[]>
     */
    private array $messages = [];

    // -------------------------------------------------------------------------
    // FailuresInterface — read side
    // -------------------------------------------------------------------------

    public function isEmpty(): bool
    {
        return $this->messages === [];
    }

    /**
     * Returns Failure value objects for one field, wrapping the stored strings.
     *
     * @return FailureInterface[]
     */
    public function forField(string $field): array
    {
        return array_map(
            static fn(string $msg) => new Failure($field, $msg),
            $this->messages[$field] ?? []
        );
    }

    /**
     * Dot-notation path lookup — equivalent to forField() since nested
     * failures are stored with dot-notation keys.
     *
     * @return FailureInterface[]
     */
    public function forPath(string $path): array
    {
        return $this->forField($path);
    }

    /**
     * Flat map: field/path → string[].
     *
     * @return array<string, string[]>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Nested map that mirrors the input data structure.
     * Splits each dot-notation key and builds nested arrays.
     *
     * @return array
     */
    public function getNestedMessages(): array
    {
        $result = [];
        foreach ($this->messages as $field => $messages) {
            $parts = explode('.', $field);
            $node  = &$result;
            foreach ($parts as $i => $part) {
                if ($i === count($parts) - 1) {
                    $node[$part] = $messages;
                } else {
                    if (! isset($node[$part]) || ! is_array($node[$part])) {
                        $node[$part] = [];
                    }
                    $node = &$node[$part];
                }
            }
            unset($node);
        }
        return $result;
    }

    // -------------------------------------------------------------------------
    // FailureCollectionInterface — write side
    // -------------------------------------------------------------------------

    /**
     * Appends a failure message for a field.
     *
     * @param string  $field   The field that failed.
     * @param string  $message The failure message.
     * @param mixed[] $args    Arguments passed to the rule (unused for storage but
     *                         forwarded to the returned Failure value object).
     */
    public function add(string $field, string $message, array $args = []): FailureInterface
    {
        $this->messages[$field][] = $message;
        return new Failure($field, $message, $args);
    }

    /**
     * Sets a single failure message for a field, replacing all previous messages.
     *
     * @param string  $field   The field that failed.
     * @param string  $message The failure message.
     * @param mixed[] $args    Arguments passed to the rule (forwarded to the returned Failure).
     */
    public function set(string $field, string $message, array $args = []): FailureInterface
    {
        $this->messages[$field] = [$message];
        return new Failure($field, $message, $args);
    }

    // -------------------------------------------------------------------------
    // Concrete helpers — not on any interface
    // -------------------------------------------------------------------------

    /**
     * Merges one or more message strings for a field.
     * Used internally by Filter::apply() and Fieldset aggregation.
     *
     * @param string|string[] $messages
     */
    public function addMessagesForField(string $field, string|array $messages): void
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
     * Returns all failure message strings for one field.
     *
     * @return string[]
     */
    public function getMessagesForField(string $field): array
    {
        return $this->messages[$field] ?? [];
    }
}
