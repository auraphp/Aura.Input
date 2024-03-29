<?php
namespace Aura\Input;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class FieldTest extends TestCase
{
    public function testAll()
    {
        $field = new Field('text');
        $field->setName('field_name')
              ->setNamePrefix('prefix')
              ->setAttribs(['foo' => 'bar'])
              ->setOptions(['baz' => 'dib'])
              ->setValue('doom');
        
        $actual = $field->get();
        
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
        
        $field->fill('irk');
        $this->assertSame('irk', $field->read());
    }
}
