<?php
namespace Aura\Input;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function newCollection()
    {
        return new Collection(function () {
            return new MockFieldset(
                new Builder,
                new Filter,
                new Options
            );
        });
    }
    
    public function testLoadAndRead()
    {
        $collection = $this->newCollection();
        
        $data = [
            ['mock_field' => 'foo'],
            ['mock_field' => 'bar'],
            ['mock_field' => 'baz'],
            ['mock_field' => 'dib'],
        ];
        
        $collection->load($data);
        
        $fieldsets = $collection->read();
        foreach ($fieldsets as $i => $fieldset) {
            $expect = $data[$i]['mock_field'];
            $actual = $fieldset->mock_field;
            $this->assertSame($expect, $actual);
        }
    }
    
    public function testLoadAndExport()
    {
        $collection = $this->newCollection();
        
        $data = [
            ['mock_field' => 'foo'],
            ['mock_field' => 'bar'],
            ['mock_field' => 'baz'],
            ['mock_field' => 'dib'],
        ];
        
        $collection->load($data);
        
        $fieldsets = $collection->export();
        foreach ($fieldsets as $i => $fieldset) {
            $expect = $data[$i]['mock_field'];
            $actual = $fieldset->mock_field;
            $this->assertSame($expect, $actual);
        }
    }
    
    public function testFilterAndGetMessages()
    {
        $collection = $this->newCollection();
        
        $data = [
            ['mock_field' => 'foo'],
            ['mock_field' => 'bar123'],
            ['mock_field' => 'baz'],
            ['mock_field' => 'dib123'],
        ];
        
        $collection->load($data);
        $actual = $collection->filter();
        $this->assertFalse($actual);
        
        $actual = $collection->getMessages();
        $expect = [
            0 => [],
            1 => [
                'mock_field' => [
                    'Use alpha only!',
                ],
            ],
            2 => [],
            3 => [
                'mock_field' => [
                    'Use alpha only!',
                ],
            ],
        ];
        
        $this->assertSame($expect, $actual);
        
        $actual = $collection->getMessages(1);
        $expect = [
            'mock_field' => [
                'Use alpha only!',
            ],
        ];
        $this->assertSame($expect, $actual);
    }
}
