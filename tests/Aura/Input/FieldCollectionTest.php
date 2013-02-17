<?php
namespace Aura\Input;

class FieldCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $fields = new FieldCollection(new FieldFactory);
        
        $fields->set('field1', 'text')
               ->attribs(['foo' => 'bar'])
               ->options(['baz' => 'dib']);
        
        $fields->set('field2', 'date')
               ->attribs(['zim' => 'gir'])
               ->options(['irk' => 'doom']);
        
        $actual = $fields->getNames();
        $expect = ['field1', 'field2'];
        $this->assertSame($expect, $actual);
        
        $actual = $fields->get('field2')->toArray();
        $expect = [
            'type' => 'date',
            'attribs' => [
                'id' => null,
                'type' => null,
                'name' => null,
                'zim' => 'gir',
            ],
            'options' => ['irk' => 'doom'],
            'label' => null,
            'label_attribs' => [],
        ];
        
        $this->assertSame($expect, $actual);
    }
}
