<?php
namespace Aura\Input;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $field = new Field('text');
        $field->setName('field_name')
              ->setNamePrefix('prefix')
              ->setAttribs(array('foo' => 'bar'))
              ->setOptions(array('baz' => 'dib'))
              ->setValue('doom');
        
        $actual = $field->get();
        
        $expect = array(
            'type' => 'text',
            'name' => 'prefix[field_name]',
            'attribs' => array(
                'id'   => null,
                'type' => null,
                'name' => null,
                'foo'  => 'bar',
            ),
            'options' => array('baz' => 'dib'),
            'value' => 'doom',
        );
        
        $this->assertSame($expect, $actual);
        
        $field->fill('irk');
        $this->assertSame('irk', $field->read());
    }
}
