<?php
namespace Aura\Input;

use StdClass;

class FormFactoryTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        $map = [
            'mock' => function () {
                return new StdClass;
            },
        ];
        $this->form_factory = new FormFactory($map);
    }

    public function test_newInstanceCanGetObject()
    {

        $actual = $this->form_factory->newInstance('mock');
        $this->assertInstanceOf('StdClass', $actual);
    }

    /**
     * @expectedException Aura\Input\Exception\NoSuchForm
     */
    public function test_newInstanceNoSuchForm()
    {
        $this->form_factory->newInstance('badname');
    }
}
