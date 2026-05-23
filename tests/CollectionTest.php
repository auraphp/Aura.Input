<?php
namespace Aura\Input;

use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function newCollection()
    {
        return new Collection(function () {
            return new MockFieldset(
                new Builder,
                new Filter
            );
        });
    }

    public function testFillAndRead()
    {
        $collection = $this->newCollection();

        $data = [
            ['foo' => 'foo1'],
            ['foo' => 'foo2'],
            ['foo' => 'foo3'],
            ['foo' => 'foo4'],
        ];

        $collection->fill($data);

        foreach ($collection->get() as $i => $fieldset) {
            $expect = $data[$i]['foo'];
            $actual = $fieldset->foo;
            $this->assertSame($expect, $actual);
        }
    }

    public function testFilterAndGetMessages(): void
    {
        $collection = $this->newCollection();

        $data = [
            ['foo' => 'foo'],
            ['foo' => 'bar123'],
            ['foo' => 'baz'],
            ['foo' => 'dib123'],
        ];

        $collection->fill($data);
        $passed = $collection->filter();
        $this->assertFalse($passed);

        // getFailures() now returns FailuresInterface with dot-notation keys:
        // failing entries are "1.foo" and "3.foo"; passing entries have no key.
        $failures = $collection->getFailures();
        $messages = $failures->getMessages();

        $this->assertSame(['Use alpha only!'], $messages['1.foo']);
        $this->assertSame(['Use alpha only!'], $messages['3.foo']);
        $this->assertArrayNotHasKey('0.foo', $messages);
        $this->assertArrayNotHasKey('2.foo', $messages);

        // getNestedMessages() reconstructs the tree
        $nested = $failures->getNestedMessages();
        $this->assertSame(['Use alpha only!'], $nested[1]['foo']);
        $this->assertSame(['Use alpha only!'], $nested[3]['foo']);
        $this->assertArrayNotHasKey('foo', $nested[0] ?? []);
        $this->assertArrayNotHasKey('foo', $nested[2] ?? []);
    }

    public function testFilterEmptyCollectionPasses(): void
    {
        $collection = $this->newCollection();
        // no fill() — collection has zero fieldsets
        $this->assertTrue($collection->filter());
        $this->assertTrue($collection->getFailures()->isEmpty());
    }

    public function testFilterAllPass(): void
    {
        $collection = $this->newCollection();

        // MockFieldset has a filter rule that requires alpha-only values
        $data = [
            ['foo' => 'alpha'],
            ['foo' => 'beta'],
            ['foo' => 'gamma'],
        ];

        $collection->fill($data);
        $this->assertTrue($collection->filter());
        $this->assertTrue($collection->getFailures()->isEmpty());
    }

    public function testArrayAccessCount()
    {
        $collection = $this->newCollection();

        $data = [
            ['foo' => 'foo'],
            ['foo' => 'bar123'],
            ['foo' => 'baz'],
            ['foo' => 'dib123'],
        ];

        $collection->fill($data);

        $this->assertSame(4, count($collection));

        $collection[0]->foo = 'changefoo';
        $this->assertSame('changefoo', $collection[0]->foo);

        $fieldset = new MockFieldset(new Builder, new Filter);
        $fieldset->foo = 'newfoo';
        $collection[0] = $fieldset;
        $this->assertSame('newfoo', $collection[0]->foo);

        $this->assertTrue(isset($collection[3]));
        unset($collection[3]);
        $this->assertFalse(isset($collection[3]));
    }
}
