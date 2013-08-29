<?php
namespace Aura\Input\Example;

use Aura\Input\Builder;
use Aura\Input\Filter;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    
    protected function setUp()
    {
        $builder = new Builder(array(
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
        ));
        
        $this->form = new ContactForm($builder, new Filter);
    }
    
    public function testAll()
    {
        // fill the form with data
        $this->form->fill(array(
            'first_name' => 'Bolivar',
            'last_name' => 'Shagnasty',
            'no_such_field' => 'nonesuch',
            'email' => 'boshag@example.com',
            'website' => 'http://boshag.example.com',
            'address' => array(
                'street' => '123 Main',
                'city' => 'Beverly Hills',
                'state' => 'CA',
                'zip' => '90210',
            ),
            'phone_numbers' => array(
                0 => array(
                    'type' => 'mobile',
                    'number' => '123-456-7890',
                ),
                1 => array(
                    'type' => 'home',
                    'number' => '234-567-8901',
                ),
                2 => array(
                    'type' => 'fax',
                    'number' => '345-678-9012',
                ),
            ),
        ));
        
        // first-level input
        $actual = $this->form->get('email');
        $expect = array(
            'type' => 'text',
            'name' => 'email',
            'attribs' => array(
                'id' => NULL,
                'type' => NULL,
                'name' => NULL,
            ),
            'options' => array(),
            'value' => 'boshag@example.com',
        );
        $this->assertSame($expect, $actual);
        
        // fieldset-level input
        $actual = $this->form->address->get('street');
        $expect = array(
            'type' => 'text',
            'name' => 'address[street]',
            'attribs' => array(
                'id' => NULL,
                'type' => NULL,
                'name' => NULL,
            ),
            'options' => array(),
            'value' => '123 Main',
        );
        $this->assertSame($expect, $actual);
        
        // collection-level input
        $actual = $this->form->phone_numbers[1]->get('number');
        $expect = array(
            'type' => 'text',
            'name' => 'phone_numbers[1][number]',
            'attribs' => array(
                'id' => NULL,
                'type' => NULL,
                'name' => NULL,
            ),
            'options' => array(),
            'value' => '234-567-8901',
        );
        $this->assertSame($expect, $actual);
        
        
    }
}
