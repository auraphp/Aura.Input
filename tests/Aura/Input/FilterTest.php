<?php
namespace Aura\Input;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    protected $filter;
    
    protected function setUp()
    {
        $this->filter = new Filter;
        
        // simple validate
        $this->filter->setRule(
            'foo',
            'Foo should be alpha only',
            function ($value) {
                return ctype_alpha($value);
            }
        );
        
        // sanitize
        $this->filter->setRule(
            'bar',
            'Remove non-alpha from bar',
            function (&$value) {
                $value = preg_replace('/[^a-z]/i', '!', $value);
                return true;
            }
        );

        // matching validate
        $this->filter->setRule(
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
        $passed = $this->filter->values($values);
        
        // 'foo' is invalid
        $this->assertFalse($passed);
        
        // get all messages
        $actual = $this->filter->getMessages();
        $expect = [
            'foo' => [
                'Foo should be alpha only',
            ]
        ];
        $this->assertSame($expect, $actual);
        
        // get just 'foo' messages
        $actual = $this->filter->getMessages('foo');
        $expect = [
            'Foo should be alpha only',
        ];
        $this->assertSame($expect, $actual);
        
        // no failures on nonexistent field
        $this->assertSame([], $this->filter->getMessages('no-such-failure'));
        
        // should have changed the values on 'bar'
        $expect = (object) [
            'foo' => 'foo_value',
            'bar' => 'bar!value',
            'baz' => 'baz_value',
            'baz_confirm' => 'baz_value',
        ];
        $this->assertEquals($expect, $values);
        
        // add some messages manually
        $this->filter->addMessages('foo', 'Added 1');
        $this->filter->addMessages('foo', ['Added 2', 'Added 3']);
        $expect = [
            'Foo should be alpha only',
            'Added 1',
            'Added 2',
            'Added 3',
        ];
        $actual = $this->filter->getMessages('foo');
        $this->assertSame($expect, $actual);
        
        // let's make it valid
        $values->foo = 'foovalue';
        $passed = $this->filter->values($values);
        $this->assertTrue($passed);
    }
    
    public function testMultipleErrorMessages()
    {
        // initial data
        $values = (object) [
            'foo' => '',
        ];
        
        // set the rule of 'foo'
        $filter = new Filter;
        $filter->setRule(
            'foo',
            'Enter Foo correctly',
            function ($value) use ($filter) {
                $pass = true;
                if ($value == '') {
                    $filter->addMessages('foo', 'Foo is required');
                    $pass = false;
                }
                
                if (! ctype_alpha($value)) {
                    $filter->addMessages('foo', 'Foo should be alpha only');
                    $pass = false;
                }
                return $pass;
            }
        );
        
        // do the values pass the filter?
        $passed = $filter->values($values);
        $this->assertFalse($passed);
        
        // get 'foo' messages
        $actual = $filter->getMessages('foo');
        $expect = [
            'Enter Foo correctly',
            'Foo is required',
            'Foo should be alpha only',
        ];
        $this->assertSame($expect, $actual);
    }

    public function testArrayOfErrorMessages()
    {
        $filter = new Filter();

        $filter->addMessages('a_field', ['the value is in wrong format', 'just another error message']);

        $actual = $filter->getMessages('a_field');

        $expected = ['the value is in wrong format', 'just another error message'];

        $this->assertEquals($expected, $actual);
    }

    public function testArrayOfErrorMessagesBeforeSingleMessage()
    {
        $filter = new Filter();

        $filter->addMessages('a_field', ['the value is in wrong format', 'another message']);
        $filter->addMessages('a_field', 'a message for the filed');

        $actual = $filter->getMessages('a_field');

        $expected = ['the value is in wrong format', 'another message', 'a message for the filed'];

        $this->assertEquals($expected, $actual);
    }

    public function testSingleMessage()
    {
        $filter = new Filter();

        $filter->addMessages('a_field', 'an error message');

        $actual = $filter->getMessages('a_field');

        $expected = ['an error message'];

        $this->assertEquals($expected, $actual);
    }
}
