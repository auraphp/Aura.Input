<?php
namespace Aura\Form;

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
        $this->assertNewInstance('Aura\Form\Field');
        $this->assertNewInstance('Aura\Form\Fieldset');
        $this->assertNewInstance('Aura\Form\Collection');
    }
}
