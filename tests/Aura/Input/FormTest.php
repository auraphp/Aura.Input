<?php
namespace Aura\Input;

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
        $csrf = new MockCsrf;
        $form->setCsrf($csrf);
        $this->assertSame($csrf, $form->getCsrf());
        
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
        $csrf = new MockCsrf;
        $form->setCsrf($csrf);
        
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
        $csrf = new MockCsrf;
        $form->setCsrf($csrf);
        
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
        $csrf = new MockCsrf;
        $form->setCsrf($csrf);
        
        // load it with a good token
        $data = ['foo' => 'bar', '__csrf_token' => 'goodvalue'];
        $form->fill($data);
        $this->assertSame('bar', $form->foo);
    }
}
