<?php
namespace Aura\Input;

class FieldsetTest extends \PHPUnit_Framework_TestCase
{
    public function newFieldset()
    {
        return new Fieldset(
            new Builder,
            new Filter
        );
    }
    
    public function testGetters()
    {
        $fieldset = $this->newFieldset();
        $this->assertInstanceOf('Aura\Input\Builder', $fieldset->getBuilder());
        $this->assertNull($fieldset->getOptions());
    }
    
    public function test__setAndGet()
    {
        $fieldset = $this->newFieldset();
        $fieldset->setField('foo');
        $fieldset->setField('bar');
        $fieldset->setField('baz');
        $fieldset->foo = 'foo_value';
        $this->assertSame('foo_value', $fieldset->foo);
    }
    
    public function test__set_noSuchInput()
    {
        $fieldset = $this->newFieldset();
        $this->setExpectedException('Aura\Input\Exception\NoSuchInput');
        $fieldset->foo = 'no such input';
    }
    
    public function test__get_noSuchInput()
    {
        $fieldset = $this->newFieldset();
        $this->setExpectedException('Aura\Input\Exception\NoSuchInput');
        $foo = $fieldset->foo;
    }
    
    public function testGet_fieldset()
    {
        $fieldset = $this->newFieldset();
        $actual = $fieldset->get();
        $this->assertSame($fieldset, $actual);
    }
    
    public function testGet_noSuchInput()
    {
        $fieldset = $this->newFieldset();
        $this->setExpectedException('Aura\Input\Exception\NoSuchInput');
        $fieldset->get('foo');
    }
    
    public function testSetFieldAndGet()
    {
        $fieldset = $this->newFieldset();
        
        $fieldset->setField('field1', 'text')
                 ->setAttribs(['foo' => 'bar'])
                 ->setOptions(['baz' => 'dib']);
        
        $fieldset->setField('field2', 'date')
                 ->setAttribs(['zim' => 'gir'])
                 ->setOptions(['irk' => 'doom']);
        
        $actual = $fieldset->get('field2');
        $expect = [
            'type' => 'date',
            'name' => 'field2',
            'attribs' => [
                'id' => null,
                'type' => null,
                'name' => null,
                'zim' => 'gir',
            ],
            'options' => ['irk' => 'doom'],
            'value' => null,
        ];
        
        $this->assertSame($expect, $actual);
        
        // get the names
        $actual = $fieldset->getInputNames();
        $expect = ['field1', 'field2'];
        $this->assertSame($expect, $actual);
    }
    
    public function testFilterAll()
    {
        // new fieldset
        $fieldset = $this->newFieldset();
        
        // add fields
        $fieldset->setField('foo');
        $fieldset->setField('bar');
        
        // get the filter and add a rule
        $filter = $fieldset->getFilter();
        $filter->setRule('foo', 'Foo should be alpha', function ($value) {
            return ctype_alpha($value);
        });
        
        // set values on the fieldset
        $fieldset->fill(['foo' => 'foo_value', 'bar' => 'bar_value']);
        
        // apply the filter
        $passed = $fieldset->filter();
        $this->assertFalse($passed);
        
        $actual = $fieldset->getMessages();
        $expect = [
            'foo' => [
                'Foo should be alpha',
            ],
        ];
        $this->assertSame($expect, $actual);
    }
    
    public function testSetFieldset()
    {
        // a map so the outer fieldset can create the inner fieldset
        $map['mock'] = function () {
            return new MockFieldset(
                new Builder,
                new Filter
            );
        };
        
        // the outer fieldset
        $fieldset = new Fieldset(
            new Builder($map),
            new Filter
        );
        
        // create an inner fieldset named 'mock'
        $fieldset->setFieldset('mock');
        
        // get the inner fieldset input
        $input = $fieldset->mock;
        $this->assertInstanceOf('Aura\Input\MockFieldset', $input);
        
        // get the 'foo' field of the inner fieldset
        $field = $input->get('foo');
        $this->assertSame('mock[foo]', $field['name']);
    }
    
    public function testSetCollection()
    {
        // a map so the outer fieldset can create the inner collection
        $map['mock'] = function () {
            return new MockFieldset(
                new Builder,
                new Filter
            );
        };
        
        // create the outer fieldset
        $fieldset = new Fieldset(
            new Builder($map),
            new Filter
        );
        
        // create an inner collection called 'mock'
        $fieldset->setCollection('mock');
        
        // get the inner collection input
        $input = $fieldset->mock;
        $this->assertInstanceOf('Aura\Input\Collection', $input);
    }
    
    public function testGetValue()
    {
        // a map so the outer fieldset can create the inner collection
        $map['mock'] = function () {
            return new MockFieldset(
                new Builder,
                new Filter
            );
        };
        
        // create the outer fieldset
        $outer = new MockFieldset(
            new Builder($map),
            new Filter
        );
        $outer->fill([
            'foo' => 'foo_val',
            'bar' => 'bar_val',
            'baz' => 'baz_val',
        ]);
        
        // create an inner collection called 'mock'
        $inner = $outer->setCollection('mock');
        $inner->fill([
            'zim' => [
                'foo' => 'foo_zim_val',
                'bar' => 'bar_zim_val',
                'baz' => 'baz_zim_val',
            ],
            'gir' => [
                'foo' => 'foo_gir_val',
                'bar' => 'bar_gir_val',
                'baz' => 'baz_gir_val',
            ],
        ]);
        
        $expect = [
            'foo' => 'foo_val',
            'bar' => 'bar_val',
            'baz' => 'baz_val',
            'mock' => [
                'zim' => [
                    'foo' => 'foo_zim_val',
                    'bar' => 'bar_zim_val',
                    'baz' => 'baz_zim_val',
                ],
                'gir' => [
                    'foo' => 'foo_gir_val',
                    'bar' => 'bar_gir_val',
                    'baz' => 'baz_gir_val',
                ],
            ],
        ];
        
        $actual = $outer->getValue();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testIssetUnset()
    {
        $fieldset = new MockFieldset(
            new Builder,
            new Filter
        );
        
        $fieldset->fill([
            'foo' => 'foo_val',
            'bar' => 'bar_val',
        ]);
        
        $this->assertTrue(isset($fieldset->foo));
        $this->assertFalse(isset($fieldset->baz));
        $this->assertFalse(isset($fieldset->no_such_field));
        
        $fieldset->baz = 'baz_val';
        $this->assertTrue(isset($fieldset->baz));
        
        unset($fieldset->baz);
        $this->assertFalse(isset($fieldset->baz));
    }
}
