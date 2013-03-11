<?php
namespace Aura\Input;

use Aura\Framework\Test\WiringAssertionsTrait;

class WiringTest extends \PHPUnit_Framework_TestCase
{
    use WiringAssertionsTrait;
    
    protected function setUp()
    {
        $this->loadDi();
    }
    
    public function testInstances()
    {
        $this->assertNewInstance('Aura\Input\Fieldset');
    }
}
