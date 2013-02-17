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
                         ->label('doom')
                         ->labelAttribs(['zim' => 'gir']);
        
        $actual = $field->toArray();
        
        $expect = [
            'type' => 'text',
            'attribs' => [
                'id'   => null,
                'type' => null,
                'name' => null,
                'foo'  => 'bar',
            ],
            'options' => ['baz' => 'dib'],
            'label' => 'doom',
            'label_attribs' => ['zim' => 'gir'],
        ];
        
        $this->assertSame($expect, $actual);
    }
}
