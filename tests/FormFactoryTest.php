<?php
namespace Aura\Input;

use StdClass;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $map = [
            'mock' => function () {
                return new StdClass;
            },
        ];
        $form_factory = new FormFactory($map);
        
        $actual = $form_factory->newInstance('mock');
        $this->assertInstanceOf('StdClass', $actual);
        
        $this->setExpectedException('Aura\Input\Exception\NoSuchForm');
        $form_factory->newInstance('badname');
    }

    public function testFormOptions() {
        $map['form'] = function ($options = null) {
            return new ConfigurableForm(
                new Builder(),
                new Filter(),
                $options
            );
        };

        $factory = new FormFactory($map);

        $form = $factory->newInstance('form', ['foo', 'bar', 'baz']);
        $this->assertInstanceOf(ConfigurableForm::class, $form);

        $field = $form->get('foo');
        $expected = ['foo', 'bar', 'baz'];
        $this->assertSame($expected, $field['options']);
    }

}
