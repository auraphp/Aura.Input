<?php
namespace Aura\Input;

class FormTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    
    protected function setUp()
    {
        $this->form = new Form(new FieldCollection(new FieldFactory));
        $this->form->setField('foo');
        $this->form->setField('bar[baz]');
        $this->form->setField('bar[dib]');
        $this->form->setField('zim[gir][irk]');
    }
    
    public function testGetFields()
    {
        $actual = $this->form->getFields();
        $expect = 'Aura\Input\FieldCollection';
        $this->assertInstanceOf($expect, $actual);
    }
    
    public function testSetAndGetValues()
    {
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
        
        $this->form->setValues($data);
        
        $actual = $this->form->getField('zim[gir][irk]');
        $expect = [
            'name' => 'zim[gir][irk]',
            'type' => 'text',
            'label' => null,
            'attribs' => [],
            'options' => [],
            'value' => 'zim.gir.irk_value',
        ];
        
        $this->assertSame($expect, $actual);
        
        $expect = $data;
        unset($expect['doom']);
        
        $actual = $this->form->getValues();
        $this->assertSame($expect, $actual);
    }
}
