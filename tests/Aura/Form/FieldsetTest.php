<?php
namespace Aura\Form;

class FieldsetTest extends \PHPUnit_Framework_TestCase
{
    public function newFieldset($class = 'Fieldset')
    {
        $class = "Aura\\Form\\$class";
        
        return new $class(
            new Builder,
            new Filter,
            new Options
        );
    }
    
    public function testGetters()
    {
      $fieldset = $this->newFieldset();
      $this->assertInstanceOf('Aura\Form\BuilderInterface', $fieldset->getBuilder());
      $this->assertInstanceOf('Aura\Form\FilterInterface', $fieldset->getFilter());
      $this->assertInstanceOf('Aura\Form\Options', $fieldset->getOptions());
      $this->assertInstanceOf('ArrayObject', $fieldset->getInputs());
    }
    
    public function test__getAndSet()
    {
        $fieldset = $this->newFieldset();
        $fieldset->setField('field1');
        
        $fieldset->field1 = 'foo';
        $this->assertSame('foo', $fieldset->field1);
    }
    
    public function testGetInput()
    {
        $fieldset = $this->newFieldset();
        $fieldset->setField('field1');
        $fieldset->setField('field2');
        $fieldset->setField('field3');
        
        $actual = $fieldset->getInput('field2')->export();
        $this->assertSame('field2', $actual['name']);
    }
    
    public function testSetField()
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
    }
    
    public function testLoadAndRead()
    {
        $fieldset = $this->newFieldset();
        $fieldset->setField('foo');
        $fieldset->setField('bar');
        
        $result = $fieldset->load([
            'foo' => 'foo_value',
            'bar' => 'bar_value',
            'baz' => 'no value',
        ]);
        
        $this->assertTrue($result);
        
        $actual = $fieldset->read();
        $this->assertSame('foo_value', $actual->foo);
        $this->assertSame('bar_value', $actual->bar);
    }
    
    public function testExport()
    {
        $fieldset = $this->newFieldset('MockFieldset');
        $fieldset->prep();
        $fieldset->load(['mock_field' => 'mock_value']);
        $actual = $fieldset->export();
        $this->assertInstanceOf('Aura\Form\Field', $actual['mock_field']);
    }
    public function testPrep()
    {
        $fieldset = $this->newFieldset('MockFieldset');
        $fieldset->prep();
        $fieldset->mock_field = 'foo';
        $this->assertSame('foo', $fieldset->mock_field);
    }
    
    public function testFilter()
    {
        $fieldset = $this->newFieldset();
        
        $fieldset->setField('foo', 'text');
        $fieldset->setField('bar', 'text');
        
        $filter = $fieldset->getFilter();
        $filter->setRule('foo', 'Foo should be alpha', function ($value) {
            return ctype_alpha($value);
        });
        
        $values = ['foo' => 'foo_value', 'bar' => 'bar_value'];
        
        $fieldset->load($values);
        
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
        $map['mock_fieldset'] = function () {
            return new MockFieldset(
                new Builder,
                new Filter,
                new Options
            );
        };
        
        $fieldset = new Fieldset(
            new Builder($map),
            new Filter,
            new Options
        );
        
        $fieldset->setFieldset('mock_fieldset');
        
        $input = $fieldset->getInput('mock_fieldset');
        $this->assertInstanceOf('Aura\Form\MockFieldset', $input);
        
        $field = $input->getInput('mock_field')->export();
        $this->assertSame('mock_fieldset[mock_field]', $field['name']);
    }
    
    public function testSetCollection()
    {
        $map['mock_fieldset'] = function () {
            return new MockFieldset(
                new Builder,
                new Filter,
                new Options
            );
        };
        
        $fieldset = new Fieldset(
            new Builder($map),
            new Filter,
            new Options
        );
        
        $fieldset->setCollection('mock_fieldset');
        
        $input = $fieldset->getInput('mock_fieldset');
        $this->assertInstanceOf('Aura\Form\Collection', $input);
    }
}
