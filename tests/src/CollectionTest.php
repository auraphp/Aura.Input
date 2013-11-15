<?php
namespace Aura\Input;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function newCollection()
    {
        return new Collection(function () {
            return new MockFieldset(
                new Builder,
                new Filter
            );
        });
    }
    
    public function testFillAndRead()
    {
        $collection = $this->newCollection();
        
        $data = array(
            array('foo' => 'foo1'),
            array('foo' => 'foo2'),
            array('foo' => 'foo3'),
            array('foo' => 'foo4'),
        );
        
        $collection->fill($data);
        
        foreach ($collection->get() as $i => $fieldset) {
            $expect = $data[$i]['foo'];
            $actual = $fieldset->foo;
            $this->assertSame($expect, $actual);
        }
    }
    
    public function testFilterAndGetMessages()
    {
        $collection = $this->newCollection();
        
        $data = array(
            array('foo' => 'foo'),
            array('foo' => 'bar123'),
            array('foo' => 'baz'),
            array('foo' => 'dib123'),
        );
        
        $collection->fill($data);
        $actual = $collection->filter();
        $this->assertFalse($actual);
        
        $actual = $collection->getMessages();
        $expect = array(
            0 => array(),
            1 => array(
                'foo' => array(
                    'Use alpha only!',
                )
            ),
            2 => array(),
            3 => array(
                'foo' => array(
                    'Use alpha only!',
                )
            )
        );
        
        $this->assertSame($expect, $actual);
        
        $actual = $collection->getMessages(1);
        $expect = array(
            'foo' => array(
                'Use alpha only!',
            )
        );
        $this->assertSame($expect, $actual);
    }
    
    public function testArrayAccessCount()
    {
        $collection = $this->newCollection();
        
        $data = array(
            array('foo' => 'foo'),
            array('foo' => 'bar123'),
            array('foo' => 'baz'),
            array('foo' => 'dib123'),
        );
        
        $collection->fill($data);
        
        $this->assertSame(4, count($collection));
        
        $collection[0]->foo = 'changefoo';
        $this->assertSame('changefoo', $collection[0]->foo);
        
        $fieldset = new MockFieldset(new Builder, new Filter);
        $fieldset->foo = 'newfoo';
        $collection[0] = $fieldset;
        $this->assertSame('newfoo', $collection[0]->foo);
        
        $this->assertTrue(isset($collection[3]));
        unset($collection[3]);
        $this->assertFalse(isset($collection[3]));
    }
}
