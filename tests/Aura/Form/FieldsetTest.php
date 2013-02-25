<?php
namespace Aura\Form;

class FieldsetTest extends \PHPUnit_Framework_TestCase
{
    public function newFieldset()
    {
        return new Fieldset(
            new Builder,
            new Filter,
            new CsrfIgnore,
            new Options
        );
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
        $fieldset->setField('baz');
        
        $data = [
            'foo' => 'foo_value',
            'bar' => 'bar.value',
            'baz' => 'baz.value',
            'doom' => 'doom'
        ];
        
        $fieldset->load($data);
        
        $actual = $fieldset->get('zim[gir][irk]');
        $expect = [
            'name' => 'zim[gir][irk]',
            'type' => 'text',
            'attribs' => [
                'id'   => null,
                'type' => null,
                'name' => null,
            ],
            'options' => [],
            'label' => null,
            'label_attribs' => [],
            'value' => 'zim.gir.irk_value',
        ];
        
        $this->assertSame($expect, $actual);
        
        $expect = $data;
        unset($expect['doom']);
        
        $actual = $fieldset->read();
        $this->assertSame($expect, $actual);
    }
    
    public function testSetAndGetValuesNotSet()
    {
        $fieldset = $this->newFieldset();
        
        $fieldset->setField('foo');
        $fieldset->setField('bar[baz]'); // should not show up
        $fieldset->setField('bar[dib]'); // should not show up
        
        $data = [
            'foo' => 'foo_value',
            'doom' => 'doom_value', // should not show up
        ];
        
        $fieldset->load($data);

        $expect = [
            'foo' => 'foo_value',
        ];

        $actual = $fieldset->read();
        $this->assertSame($expect, $actual);
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
}
