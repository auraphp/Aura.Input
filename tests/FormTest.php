<?php
namespace Aura\Input;

use Aura\Input\Example\AddressFieldset;
use Aura\Input\Example\ContactFilter;
use Aura\Input\Example\ContactForm;
use Aura\Input\Example\PhoneFieldset;
use Aura\Input\Example\PhoneFilter;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testAddCsrf()
    {
        // set up the basic form
        $form = new Form(new Builder, new Filter);
        $form->setField('foo');

        // there should be only one field
        $expect = ['foo'];
        $actual = $form->getInputNames();
        $this->assertSame($expect, $actual);

        // set CSRF into the form
        $csrf = new MockAntiCsrf;
        $form->setAntiCsrf($csrf);
        $this->assertSame($csrf, $form->getAntiCsrf());

        // there should be two fields now
        $expect = ['foo', '__csrf_token'];
        $actual = $form->getInputNames();
        $this->assertSame($expect, $actual);
    }

    public function testMissingCsrf()
    {
        // set up the basic form
        $form = new Form(new Builder, new Filter);
        $form->setField('foo');

        // set CSRF into the form
        $csrf = new MockAntiCsrf;
        $form->setAntiCsrf($csrf);

        // load it with a missing csrf token
        $data = ['foo' => 'bar'];
        $this->setExpectedException('Aura\Input\Exception\CsrfViolation');
        $form->fill($data);
    }

    public function testBadCsrf()
    {
        // set up the basic form
        $form = new Form(new Builder, new Filter);
        $form->setField('foo');

        // set CSRF into the form
        $csrf = new MockAntiCsrf;
        $form->setAntiCsrf($csrf);

        // load it with a bad token
        $data = ['foo' => 'bar', '__csrf_token' => 'badvalue'];
        $this->setExpectedException('Aura\Input\Exception\CsrfViolation');
        $form->fill($data);
    }

    public function testGoodCsrf()
    {
        // set up the basic form
        $form = new Form(new Builder, new Filter);
        $form->setField('foo');

        // set CSRF into the form
        $csrf = new MockAntiCsrf;
        $form->setAntiCsrf($csrf);

        // load it with a good token
        $data = ['foo' => 'bar', '__csrf_token' => 'goodvalue'];
        $form->fill($data);
        $this->assertSame('bar', $form->foo);
    }

    public function testGetIterator()
    {
        // set up the basic form
        $form = new Form(new Builder, new Filter);
        $form->setField('foo');
        $form->setField('bar');

        $iterator = $form->getIterator();
        $keys = array_keys($iterator->getArrayCopy());
        $this->assertSame(['foo', 'bar'], $keys);
    }

    public function testGetFailures()
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
                    new PhoneFilter
                );
            },
        ]);

        $filter = new ContactFilter();

        $form = new ContactForm($builder, $filter);
        // fill the form with data
        $form->fill([
            'first_name' => '',
            'last_name' => 'KT',
            'no_such_field' => 'nonesuch',
            'email' => 'someon@example.com',
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
                    'number' => '5345a34-8988',
                ],
            ],
        ]);

        $this->assertFalse($form->filter());
        $failures = $form->getFailures();
        $this->assertSame(2, $failures->count());
        $this->assertSame("First name must be alphabetic only.", $failures->offsetGet('first_name')[0]);
        $this->assertSame("Not a valid phone number.", $failures['phone_numbers'][2]['number'][0]);
    }
}
