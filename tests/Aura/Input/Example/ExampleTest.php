<?php
namespace Aura\Input\Example;

use Aura\Input\Builder;
use Aura\Input\Filter;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    
    protected function setUp()
    {
        $builder = new Builder([
            'address' => function () {
                return new AddressFieldset(
                    new Builder,
                    new Filter
                );
            },
            'phone' => function () {
                return new PhoneFieldset(
                    new Builder,
                    new Filter
                );
            },
        ]);
        
        $this->form = new ContactForm($builder, new Filter);
    }
    
    public function testAll()
    {
        // fill the form with data
        $this->form->fill([
            'first_name' => 'Bolivar',
            'last_name' => 'Shagnasty',
            'no_such_field' => 'nonesuch',
            'email' => 'boshag@example.com',
            'website' => 'http://boshag.example.com',
            'address' => [
                'street' => '123 Main',
                'city' => 'Beverly Hills',
                'state' => 'CA',
                'zip' => '90210',
            ],
            'phone_numbers' => [
                0 => [
                    'type' => 'mobile',
                    'number' => '123-456-7890',
                ],
                1 => [
                    'type' => 'home',
                    'number' => '234-567-8901',
                ],
                2 => [
                    'type' => 'fax',
                    'number' => '345-678-9012',
                ],
            ],
        ]);
        
        // first-level input
        $actual = $this->form->get('email');
        $expect = [
            'type' => 'text',
            'name' => 'email',
            'attribs' => [
                'id' => NULL,
                'type' => NULL,
                'name' => NULL,
            ],
            'options' => [],
            'value' => 'boshag@example.com',
        ];
        $this->assertSame($expect, $actual);
        
        // fieldset-level input
        $actual = $this->form->address->get('street');
        $expect = [
            'type' => 'text',
            'name' => 'address[street]',
            'attribs' => [
                'id' => NULL,
                'type' => NULL,
                'name' => NULL,
            ],
            'options' => [],
            'value' => '123 Main',
        ];
        $this->assertSame($expect, $actual);
        
        // collection-level input
        $actual = $this->form->phone_numbers[1]->get('number');
        $expect = [
            'type' => 'text',
            'name' => 'phone_numbers[1][number]',
            'attribs' => [
                'id' => NULL,
                'type' => NULL,
                'name' => NULL,
            ],
            'options' => [],
            'value' => '234-567-8901',
        ];
        $this->assertSame($expect, $actual);
        
        
    }
}
