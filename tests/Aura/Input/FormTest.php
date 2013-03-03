<?php
namespace Aura\Input;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        // set up the basic form
        $form = new Form(new Builder, new Filter, new Options);
        $form->setField('foo');
        
        // there should be only one field
        $expect = ['foo'];
        $actual = $form->getInputNames();
        $this->assertSame($expect, $actual);
        
        // set CSRF into the form
        $csrf = new MockCSrf;
        $form->setCsrf($csrf);
        $this->assertSame($csrf, $form->getCsrf());
        
        // there should be two fields now
        $expect = ['foo', '__csrf_token'];
        $actual = $form->getInputNames();
        $this->assertSame($expect, $actual);
        
        // load it with a missing csrf token
        $data = ['foo' => 'bar'];
        $this->assertFalse($form->load($data));
        $this->assertNull($form->foo);
        
        // load it with a bad token    
        $data = ['foo' => 'bar', '__csrf_token' => 'badvalue'];
        $this->assertFalse($form->load($data));
        $this->assertNull($form->foo);
        
        // load it with a good token
        $data = ['foo' => 'bar', '__csrf_token' => 'goodvalue'];
        $this->assertTrue($form->load($data));
        $this->assertSame('bar', $form->foo);
    }
}
