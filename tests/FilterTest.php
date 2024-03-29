<?php
namespace Aura\Input;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class FilterTest extends TestCase
{
    protected $filter;

    protected function set_up()
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

        // sanitize
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

    public function testAll()
    {
        // initial data
        $values = (object) [
            'foo' => 'foo_value',
            'bar' => 'bar_value',
            'baz' => 'baz_value',
            'baz_confirm' => 'baz_value',
        ];

        // do the values pass all filters?
        $passed = $this->filter->apply($values);

        // 'foo' is invalid
        $this->assertFalse($passed);

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

        // should have changed the values on 'bar'
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
        $passed = $this->filter->apply($values);
        $this->assertTrue($passed);
        $this->assertTrue($this->filter->getFailures()->isEmpty());
    }

    public function testMultipleErrorMessages()
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
        $passed = $filter->apply($values);
        $this->assertFalse($passed);

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
