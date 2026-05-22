<?php
namespace Aura\Input;

use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    protected Filter $filter;

    protected function setUp(): void
    {
        $this->filter = new Filter;

        // simple validate
        $this->filter->addRule(
            'foo',
            'Foo should be alpha only',
            function ($value) {
                return ctype_alpha($value);
            }
        );

        // sanitize — closure modifies the value via by-ref first parameter
        $this->filter->addRule(
            'bar',
            'Remove non-alpha from bar',
            function (&$value) {
                $value = preg_replace('/[^a-z]/i', '!', $value);
                return true;
            }
        );

        // matching validate
        $this->filter->addRule(
            'baz_confirm',
            'Baz confirm must match baz',
            function ($value, $fields) {
                return $value == $fields->baz;
            }
        );
    }

    public function testAll(): void
    {
        // initial data
        $values = (object) [
            'foo' => 'foo_value',
            'bar' => 'bar_value',
            'baz' => 'baz_value',
            'baz_confirm' => 'baz_value',
        ];

        // apply() now returns a FilterResultInterface
        $result = $this->filter->apply($values);

        // 'foo' is invalid
        $this->assertFalse($result->isSuccess());

        // get all messages
        $actual = $this->filter->getFailures()->getMessages();
        $expect = [
            'foo' => [
                'Foo should be alpha only',
            ]
        ];
        $this->assertEquals($expect, $actual);

        // get just 'foo' messages
        $actual = $this->filter->getFailures()->getMessagesForField('foo');
        $expect = [
            'Foo should be alpha only',
        ];
        $this->assertSame($expect, $actual);

        // no failures on nonexistent field
        $this->assertTrue(empty($this->filter->getFailures()->getMessagesForField('no-such-failure')));

        // should have changed the value on 'bar' (closure uses &$value which
        // modifies the object property in place via the PHP reference mechanism)
        $expect = (object) [
            'foo' => 'foo_value',
            'bar' => 'bar!value',
            'baz' => 'baz_value',
            'baz_confirm' => 'baz_value',
        ];
        $this->assertEquals($expect, $values);

        // add some messages manually
        $this->filter->getFailures()->addMessagesForField('foo', 'Added 1');
        $this->filter->getFailures()->addMessagesForField('foo', ['Added 2', 'Added 3']);
        $expect = [
            'Foo should be alpha only',
            'Added 1',
            'Added 2',
            'Added 3',
        ];
        $actual = $this->filter->getFailures()->getMessagesForField('foo');
        $this->assertSame($expect, $actual);

        // let's make it valid
        $values->foo = 'foovalue';
        $result = $this->filter->apply($values);
        $this->assertTrue($result->isSuccess());
        $this->assertTrue($this->filter->getFailures()->isEmpty());
    }

    public function testFailureCollectionAddAndSet(): void
    {
        $failures = new \Aura\Input\Filter\FailureCollection;
        $this->assertTrue($failures->isEmpty());

        $first = $failures->add('foo', 'first message', ['arg' => 1]);
        $this->assertInstanceOf(\Aura\Filter_Interface\FailureInterface::class, $first);
        $this->assertSame('foo', $first->getField());
        $this->assertSame('first message', $first->getMessage());
        $this->assertSame(['arg' => 1], $first->getArgs());

        $failures->add('foo', 'second message');
        $this->assertSame(['first message', 'second message'], $failures->getMessagesForField('foo'));

        $replaced = $failures->set('foo', 'only message');
        $this->assertInstanceOf(\Aura\Filter_Interface\FailureInterface::class, $replaced);
        $this->assertSame(['only message'], $failures->getMessagesForField('foo'));

        $this->assertSame(
            ['field' => 'foo', 'message' => 'only message', 'args' => []],
            $replaced->jsonSerialize()
        );
    }

    public function testMultipleErrorMessages(): void
    {
        // initial data
        $values = (object) [
            'foo' => '',
        ];

        // set the rule of 'foo'
        $filter = new Filter;
        $filter->addRule(
            'foo',
            'Enter Foo correctly',
            function ($value) use ($filter) {
                $pass = true;
                if ($value == '') {
                    $filter->getFailures()->addMessagesForField('foo', 'Foo is required');
                    $pass = false;
                }

                if (! ctype_alpha($value)) {
                    $filter->getFailures()->addMessagesForField('foo', 'Foo should be alpha only');
                    $pass = false;
                }
                return $pass;
            }
        );

        // do the values pass the filter?
        $result = $filter->apply($values);
        $this->assertFalse($result->isSuccess());

        // get 'foo' messages
        $actual = $filter->getFailures()->getMessagesForField('foo');
        $expect = [
            'Foo is required',
            'Foo should be alpha only',
            'Enter Foo correctly',
        ];
        $this->assertSame($expect, $actual);
    }
}
