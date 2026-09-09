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
 * Stores Failure objects keyed by field name or dot-notation path, so the
 * arguments passed to add()/set() are retained and returned by forField()
 * and forPath(). Implements the full FailureCollectionInterface, satisfying
 * both the write (add/set) and read
 * (forField/forPath/getMessages/getNestedMessages) sides.
 *
 * The bundled closure-based Filter records failures without arguments, so in
 * ordinary use every Failure carries an empty argument list; the storage keeps
 * whatever a caller supplies rather than discarding it.
 *
 * @package Aura.Input
 *
 */
class FailureCollection implements FailureCollectionInterface
{
    /**
     * Failures keyed by field name or dot-notation path → Failure[].
     *
     * @var array<string, FailureInterface[]>
     */
    private array $items = [];

    // -------------------------------------------------------------------------
    // FailuresInterface — read side
    // -------------------------------------------------------------------------

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /**
     * Returns all Failure objects recorded for a field.
     *
     * Returns an empty array when no failures exist for the given key.
     *
     * @return FailureInterface[]
     */
    public function forField(string $field): array
    {
        return $this->items[$field] ?? [];
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
        $messages = [];
        foreach ($this->items as $field => $failures) {
            $messages[$field] = array_map(
                static fn(FailureInterface $failure) => $failure->getMessage(),
                $failures
            );
        }
        return $messages;
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
        foreach ($this->getMessages() as $field => $messages) {
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
     * Appends a failure for a field.
     *
     * @param string  $field   The field that failed.
     * @param string  $message The failure message.
     * @param mixed[] $args    Arguments passed to the rule; stored on the Failure.
     */
    public function add(string $field, string $message, array $args = []): FailureInterface
    {
        $failure               = new Failure($field, $message, $args);
        $this->items[$field][] = $failure;
        return $failure;
    }

    /**
     * Sets a single failure for a field, replacing all previous failures.
     *
     * @param string  $field   The field that failed.
     * @param string  $message The failure message.
     * @param mixed[] $args    Arguments passed to the rule; stored on the Failure.
     */
    public function set(string $field, string $message, array $args = []): FailureInterface
    {
        $failure             = new Failure($field, $message, $args);
        $this->items[$field] = [$failure];
        return $failure;
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
        foreach ((array) $messages as $message) {
            $this->add($field, $message);
        }
    }

    /**
     * Returns all failure message strings for one field.
     *
     * @return string[]
     */
    public function getMessagesForField(string $field): array
    {
        return array_map(
            static fn(FailureInterface $failure) => $failure->getMessage(),
            $this->forField($field)
        );
    }
}
