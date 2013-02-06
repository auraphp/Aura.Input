<?php
namespace Aura\Input;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $factory = new FieldFactory;
        
        $field = $factory->newInstance('text')
                         ->attribs(['foo' => 'bar'])
                         ->options(['baz' => 'dib']);
        
        $actual = $field->asArray();
        
        $expect = [
            'type' => 'text',
            'attribs' => ['foo' => 'bar'],
            'options' => ['baz' => 'dib'],
        ];
        
        $this->assertSame($expect, $actual);
    }
}
