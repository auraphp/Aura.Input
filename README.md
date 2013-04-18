Aura.Input
==========

This package contains tools to describe and filter user inputs from an HTML
form, including sub-forms/sub-fieldsets, fieldset collections, an interface
for injecting custom filter systems, and CSRF protection. Note that this
package does not include output functionality, although the "hints" provided
by the `Form` object can be used with any presentation system to generate an
HTML form.

This package is compliant with [PSR-0][], [PSR-1][], and [PSR-2][]. If you
notice compliance oversights, please send a patch via pull request.

[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

Getting Started
===============

Instantiation
-------------

The easiest way to instantiate a new `Form` is to include the `instance.php`
script:

```php
<?php
$form = require "/path/to/Aura.Input/scripts/instance.php";
```

Alternatively, we can add the `Aura.Input` package to an autoloader, and
instantiate manually:

```php
<?php
use Aura\Input\Form;
use Aura\Input\Builder;
use Aura\Input\Filter;

$form = new Form(new Builder, new Filter);
```

Setting Input Fields On The Form
--------------------------------

Use the `setField()` method to add an input field to the form.

```php
<?php
$form->setField('first_name')
$form->setField('last_name');
$form->setField('email');
$form->setField('email_confirm');
$form->setField('website');
$form->setField('street_address');
$form->setField('city');
$form->setField('state');
$form->setField('zip');
$form->setField('phone_number');
$form->setField('phone_type');
$form->setField('birthday');
```

(We will discuss later how to set the field type, attributes, and options;
these will provide hints to our view layer on how to present the field.)


Setting Filters On The Form
---------------------------

Aura.Input comes with a very basic filter system. Use the `getFilter()` method
to get the `Filter` object, then add rules to the filter using the `setRule()`
method.

Rules are closures that test a form input value. The first parameter is the
name of the form field to test; the second parameter is the message to use if
the rule fails; the third parameter is a closure to test the form input value.
The closure should return `true` if the rule passes, or `false` if it does
not, and it should take two parameters: the value of the field being tested,
and the set of all fields (in case we need to compare to other inputs).

```php
<?php
$filter = $form->getFilter();

$filter->setRule(
    'first_name',
    'First name must be alphabetic only.',
    function ($value, $fields) {
        return ctype_alpha($value);
    }
);

$filter->setRule(
    'last_name',
    'Last name must be alphabetic only.',
    function ($value, $fields) {
        return ctype_alpha($value);
    }
);

$filter->setRule(
    'email_confirm',
    'The email addresses must match.',
    function ($value, $fields) {
        return $value == $fields->email;
    }
);

$filter->setRule(
    'state',
    'State not recognized.',
    function ($value, $form) {
        $states = [
            'AK', 'AL', 'AR', 'AZ',
            // ...
            'WA', 'WI', 'WV', 'WY',
        ];
        return in_array($value, $states);
    }
);

$filter->setRule(
    'zip',
    'ZIP code must be between 00000 and 99999.',
    function ($value, $form) {
        return ctype_digit($value)
            && $value >= 00000
            && $value <= 99999;
    }
);

$filter->setRule(
    'phone_type',
    'Phone type not recognized.',
    function ($value, $form) {
        $types = ['cell', 'home', 'work'];
        return in_array($value, $types);
    }
);

$filter->setRule(
    'birthday',
    'Birthday is not a valid date.',
    function ($value, $form) {
        $datetime = date_create($value);
        if (! $datetime) {
            return false;
        }
        $errors = \DateTime::getLastErrors();
        if ($errors['warnings']) {
            return false;
        }
        return true;
    }
);
```

(We will discuss later how to implement `FilterInterface` and use our own
filters.)


Populating and Validating User Input
------------------------------------

Now that we have input fields, and filters for those inputs, we can fill the
form with user input and see if the user input is valid. First, we use the
`fill()` method to set the input values. We then call the `filter()` method to
see if the user input is valid; if not, we show the messages for the inputs
that did not pass their filter rules.

```php
<?php
// fill the form with $_POST array elements
// that match the form input names.
$form->fill($_POST);

// apply the filters
$pass = $form->filter();

// did all the filters pass?
if ($pass) {
    // yes
    echo "User input is valid." . PHP_EOL;
} else {
    // no; get the messages.
    echo "User input is not valid." . PHP_EOL;
    foreach ($form->getMessages() as $name => $messages) {
        foreach ($messages as $message) {
            echo "Input '{$name}': {$message}" . PHP_EOL;
        }
    }
}
```

Advanced Usage
==============

Self-Initializing Forms
-----------------------

In the "Getting Started" example, we create a form object and then manipulate
it to add inputs.  While perfectly reasonable, sometimes we will want to have
a form object initialize its own inputs and filters.  To do this, extend
the `Form` object and override the `init()` method.

```php
<?php
namespace Vendor\Package;

use Aura\Input\Form;

class ContactForm extends Form
{
    protected function init()
    {
        // set input fields
        $this->setField('first_name')
        $this->setField('last_name');
        // etc.
        
        // set input filters
        $filter = $this->getFilter();
        $filter->setRule(
            'first_name',
            'First name must be alphabetic only.',
            function ($value) {
                return ctype_alpha($value);
            }
        );
        // etc.
    }
}
```

Now when we instantiate the `ContactForm` the inputs and filters will be there
automatically.


Applying CSRF Protections
-------------------------

Aura.Input comes with an interface for implementations that prevent
[cross-site request forgery](https://www.owasp.org/index.php/Cross-Site_Request_Forgery)
attacks.  To make use of this interface, we will need to provide our own
CSRF implentation; this is because it depends on two things that Aura.Input
cannot provide: an object that tells us if the user is authenticated or not,
and an object to generate and retain a crytpographically secure random value
for the CSRF token value.  A psuedo-implementation follows.

```php
<?php
namespace Vendor\Package\Input;

use Aura\Input\AntiCsrfInterface;
use Aura\Input\Fieldset;
use Vendor\Package\CsrfObject;
use Vendor\Package\UserObject;

class AntiCsrf implements AntiCsrfInterface
{
    // a user object indicating if the user is authenticated or not
    protected $user;
    
    // a csrf value generation object
    protected $csrf;
    
    public function __construct(UserObject $user, CsrfObject $csrf)
    {
        $this->user = $user;
        $this->csrf = $csrf;
    }
    
    // implementation of setField(); adds a CSRF token field to the fieldset.
    public function setField(Fieldset $fieldset)
    {
        if (! $this->user->isAuthenticated()) {
            // user is not authenticated so CSRF cannot occur
            return;
        }
        
        // user is authenticated, so add a CSRF token
        $fieldset->setField('__csrf_token', $this->csrf->getValue());
    }
    
    // implementation of isValid().  return true if CSRF token is present
    // and of the correct value, or return false if not.
    public function isValid(array $data)
    {
        if (! $this->user->isAuthenticated()) {
            // user is not authenticated so CSRF cannot occur
            return true;
        }
        
        // user is authenticated, so check to see if input has a CSRF token
        // of the correct value
        return isset($data['__csrf_token'])
            && $data['__csrf_token'] == $this->csrf->getValue();
    }
}
```

We can then pass an instance of the implementation into our form using the
`setAntiCsrf()` method.

```php
<?php
use Aura\Input\Form;
use Aura\Input\Builder;
use Aura\Input\Filter;
use Vendor\Package\Input\AntiCsrf;
use Vendor\Package\UserObject;
use Vendor\Package\CsrfObject;

$form = new Form(new Builder, new Filter);

$anti_csrf = new AntiCsrf(new UserObject, new CsrfObject);
$form->setAntiCsrf($anti_csrf);
```

Calling `setAntiCsrf()` adds a CSRF field to the form.

When we call `fill()` on the form, it will check the CSRF value in the data
to make sure it is correct.  If not, the form will not fill in the data, and
throw an exception and will not fill in the data.


Providing "Hints" To The View Layer
-----------------------------------

TBD.


Passing Options Into Forms
--------------------------

TBD.


Creating Reusable Fieldsets
---------------------------

TBD.


Using Fieldset Collections
--------------------------

TBD.

