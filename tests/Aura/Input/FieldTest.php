<?php
namespace Aura\Input;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $field = new Field('text');
        $field->setName('field_name')
              ->setArrayName('prefix')
              ->setAttribs(['foo' => 'bar'])
              ->setOptions(['baz' => 'dib'])
              ->setValue('doom');
        
        $actual = $field->export();
        
        $expect = [
            'type' => 'text',
            'name' => 'prefix[field_name]',
            'attribs' => [
                'id'   => null,
                'type' => null,
                'name' => null,
                'foo'  => 'bar',
            ],
            'options' => ['baz' => 'dib'],
            'value' => 'doom',
        ];
        
        $this->assertSame($expect, $actual);
        
        $field->load('irk');
        $this->assertSame('irk', $field->read());
    }
}
