<?php
namespace Aura\Input;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function newForm()
    {
        return new Form(
            new FieldCollection(new FieldFactory),
            new Filter
        );
    }
    
    public function testGetFields()
    {
        $form = $this->newForm();
        $actual = $form->getFields();
        $expect = 'Aura\Input\FieldCollection';
        $this->assertInstanceOf($expect, $actual);
    }
    
    public function testSetAndGetValues()
    {
        $form = $this->newForm();
        
        $form->setField('foo');
        $form->setField('bar[baz]');
        $form->setField('bar[dib]');
        $form->setField('zim[gir][irk]');
        
        $data = [
            'foo' => 'foo_value',
            'bar' => [
                'baz' => 'bar.baz_value',
                'dib' => 'bar.dib_value',
            ],
            'zim' => [
                'gir' => [
                    'irk' => 'zim.gir.irk_value'
                ],
            ],
            'doom' => 'doom_value', // should not show up
        ];
        
        $form->setValues($data);
        
        $actual = $form->getField('zim[gir][irk]');
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
        
        $actual = $form->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testSetAndGetValuesNotSet()
    {
        $form = $this->newForm();
        
        $form->setField('foo');
        $form->setField('bar[baz]');
        $form->setField('bar[dib]');
        
        $data = [
            'foo' => 'foo_value',
            'doom' => 'doom_value', // should not show up
        ];
        
        $form->setValues($data);

        $expect = [
            'foo' => 'foo_value',
            'bar[baz]' => null,
            'bar[dib]' => null,
        ];

        $actual = $form->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testFilter()
    {
        $form = $this->newForm();
        
        $form->setField('foo', 'text');
        $form->setField('bar', 'text');
        
        $filter = $form->getFilter();
        $filter->setRule('foo', 'Foo should be alpha', function ($value) {
            return ctype_alpha($value);
        });
        
        $values = ['foo' => 'foo_value', 'bar' => 'bar_value'];
        
        $form->setValues($values);
        
        $passed = $form->filter();
        $this->assertFalse($passed);
        
        $actual = $form->getMessages();
        $expect = [
            'foo' => [
                'Foo should be alpha',
            ],
        ];
        $this->assertSame($expect, $actual);
    }
}
