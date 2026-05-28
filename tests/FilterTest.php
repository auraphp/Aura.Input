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

    public function testSetRuleReplacesExistingRules(): void
    {
        $filter = new Filter;

        // add two rules for 'foo'
        $filter->addRule('foo', 'First rule', fn($v) => false);
        $filter->addRule('foo', 'Second rule', fn($v) => false);

        // setRule() must discard both and install only the new one
        $filter->setRule('foo', 'Only rule', fn($v) => false);

        $values = (object) ['foo' => 'anything'];
        $result = $filter->apply($values);

        $this->assertFalse($result->isSuccess());
        $messages = $filter->getFailures()->getMessagesForField('foo');
        $this->assertSame(['Only rule'], $messages);
    }

    public function testApplyGetValuesReturnsSanitizedSubject(): void
    {
        $filter = new Filter;

        // sanitize rule: strip non-alpha characters in-place via reference
        $filter->addRule('bar', 'bar sanitized', function (&$value) {
            $value = preg_replace('/[^a-z]/i', '', $value);
            return true;
        });

        $values = (object) ['bar' => 'b4r!'];
        $result = $filter->apply($values);

        $this->assertTrue($result->isSuccess());
        // getValues() must return the same object (Fieldset/stdClass handle)
        $this->assertSame($values, $result->getValues());
        // the sanitize rule wrote back through the reference, mutating the object
        $this->assertSame('br', $values->bar);
    }

    public function testGetMessagesSingleField(): void
    {
        $filter = new Filter;
        $filter->addRule('foo', 'Foo must be alpha', fn($v) => ctype_alpha($v));
        $filter->addRule('bar', 'Bar must be alpha', fn($v) => ctype_alpha($v));

        $values = (object) ['foo' => '123', 'bar' => '456'];
        $filter->apply($values);

        // single-field variant returns only that field's messages
        $this->assertSame(['Foo must be alpha'], $filter->getMessages('foo'));
        $this->assertSame(['Bar must be alpha'], $filter->getMessages('bar'));
        // all-fields variant returns both
        $this->assertArrayHasKey('foo', $filter->getMessages());
        $this->assertArrayHasKey('bar', $filter->getMessages());
    }

    public function testFailureCollectionForFieldAndForPath(): void
    {
        $filter = new Filter;
        $filter->addRule('email', 'Email is required', fn($v) => $v !== '');

        $values = (object) ['email' => ''];
        $filter->apply($values);

        $failures = $filter->getFailures();

        // forField() returns FailureInterface objects
        $byField = $failures->forField('email');
        $this->assertCount(1, $byField);
        $this->assertInstanceOf(\Aura\Filter_Interface\FailureInterface::class, $byField[0]);
        $this->assertSame('email', $byField[0]->getField());
        $this->assertSame('Email is required', $byField[0]->getMessage());

        // forPath() is an alias with the same behaviour
        $byPath = $failures->forPath('email');
        $this->assertCount(1, $byPath);
        $this->assertSame('email', $byPath[0]->getField());

        // non-existent field returns empty array for both
        $this->assertSame([], $failures->forField('no_such_field'));
        $this->assertSame([], $failures->forPath('no_such_field'));
    }
}
