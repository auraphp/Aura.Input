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
}
