<?php
namespace Aura\Input;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    protected $filter;
    
    protected function setUp()
    {
        $this->filter = new Filter;
        
        // validate
        $this->filter->addRule('foo', 'Foo should be alpha only', function ($value) {
            return ctype_alpha($value);
        });
        
        $this->filter->addRule('foo', 'Foo should be more than 7 characters', function ($value) {
            $length = strlen($value);
            return ($length > 7);
        });
        
        // sanitize
        $this->filter->addRule('bar', 'Remove non-alpha from bar', function (&$value) {
            $value = preg_replace('/[^a-z]/i', '!', $value);
            return true;
        });
    }
    
    public function testAll()
    {
        // initial data
        $values = [
            'foo' => 'foo_value',
            'bar' => 'bar_value',
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
        
        // no failures on this field
        $this->assertSame([], $this->filter->getMessages('no-such-failure'));
        
        // should have changed the values on 'bar'
        $expect = [
            'foo' => 'foo_value',
            'bar' => 'bar!value',
        ];
        $this->assertSame($expect, $values);
        
        // let's make it valid
        $data['foo'] = 'foovalue';
        $passed = $this->filter->values($data);
        $this->assertTrue($passed);
        
        $data['foo'] = 'fooval';
        $passed = $this->filter->values($data);
        $this->assertFalse($passed);
        $expect = [
            'Foo should be more than 7 characters',
        ];
        $this->assertSame($expect, $this->filter->getMessages('foo'));
        
        // Get more errors
        $values = [
            'foo' => 'foo_va',
            'bar' => 'bar_value',
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
                'Foo should be more than 7 characters'
            ]
        ];
        $this->assertSame($expect, $actual);
        
    }
}
