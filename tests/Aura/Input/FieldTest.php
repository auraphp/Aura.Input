<?php
namespace Aura\Input;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $factory = new FieldFactory;
        
        $field = $factory->newInstance('text')
                         ->attribs(['foo' => 'bar'])
                         ->options(['baz' => 'dib'])
                         ->label('DOOM');
        
        $actual = $field->asArray();
        
        $expect = [
            'type' => 'text',
            'label' => 'DOOM',
            'attribs' => ['foo' => 'bar'],
            'options' => ['baz' => 'dib'],
        ];
        
        $this->assertSame($expect, $actual);
    }
}
