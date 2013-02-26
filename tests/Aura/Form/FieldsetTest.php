<?php
namespace Aura\Form;

class FieldsetTest extends \PHPUnit_Framework_TestCase
{
    public function newFieldset()
    {
        return new Fieldset(
            new Builder,
            new Filter,
            new MockCsrf,
            new Options
        );
    }
    
    public function testGetters()
    {
      $fieldset = $this->newFieldset();
      $this->assertInstanceOf('Aura\Form\BuilderInterface', $fieldset->getBuilder());
      $this->assertInstanceOf('Aura\Form\FilterInterface', $fieldset->getFilter());
      $this->assertInstanceOf('Aura\Form\CsrfInterface', $fieldset->getCsrf());
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
            '__csrf_token' => 'goodvalue',
        ]);
        
        $this->assertTrue($result);
        
        $actual = $fieldset->read();
        $this->assertSame('foo_value', $actual->foo);
        $this->assertSame('bar_value', $actual->bar);
    }
    
    public function testLoadCsrf()
    {
        $fieldset = $this->newFieldset();
        $csrf = $fieldset->getCsrf();
        $csrf->setField($fieldset);
        
        $fieldset->setField('foo');
        $fieldset->setField('bar');
        
        // before loading
        $this->assertSame('goodvalue', $fieldset->__csrf_token);
        
        // load it
        $result = $fieldset->load([
            'foo' => 'foo_value',
            'bar' => 'bar_value',
            'baz' => 'no value',
            '__csrf_token' => 'badvalue', // does not match the correct value
        ]);
        
        // should fail to load
        $this->assertFalse($result);
        
        // the values are still at their defaults
        $this->assertNull($fieldset->foo);
        $this->assertNull($fieldset->bar);
        $this->assertSame('goodvalue', $fieldset->__csrf_token);
    }
    
    // public function testFilter()
    // {
    //     $fieldset = $this->newFieldset();
    //     
    //     $fieldset->setField('foo', 'text');
    //     $fieldset->setField('bar', 'text');
    //     
    //     $filter = $fieldset->getFilter();
    //     $filter->setRule('foo', 'Foo should be alpha', function ($value) {
    //         return ctype_alpha($value);
    //     });
    //     
    //     $values = ['foo' => 'foo_value', 'bar' => 'bar_value'];
    //     
    //     $fieldset->load($values);
    //     
    //     $passed = $fieldset->filter();
    //     $this->assertFalse($passed);
    //     
    //     $actual = $fieldset->getMessages();
    //     $expect = [
    //         'foo' => [
    //             'Foo should be alpha',
    //         ],
    //     ];
    //     $this->assertSame($expect, $actual);
    // }
}
