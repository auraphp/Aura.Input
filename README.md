Aura.Input
==========

[![Build Status](https://travis-ci.org/auraphp/Aura.Input.png?branch=develop)](https://travis-ci.org/auraphp/Aura.Input)

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
?>
```

Alternatively, we can add the `Aura.Input` package to an autoloader, and
instantiate manually:

```php
<?php
use Aura\Input\Form;
use Aura\Input\Builder;
use Aura\Input\Filter;

$form = new Form(new Builder, new Filter);
?>
```

Setting Input Fields On The Form
--------------------------------

Use the `setField()` method to add an input field to the form.

```php
<?php
$form->setField('first_name');
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
?>
```

(We will discuss later how to set the field type, attributes, and options;
these will provide hints to our view layer on how to present the field.)


Setting Filters On The Form
---------------------------

Aura.Input comes with a very basic filter system. Use the `getFilter()` method
to get the `Filter` object, then add rules to the filter using the `setRule()`
method. The first parameter to `setRule()` is the name of the form field to
test; the second parameter is the message to use if the rule fails; the third
parameter is a closure to test the form input value.

The closure for the rule should take two parameters: the value of the field
being tested, and optionally the set of all fields in the form (in case we
need to compare to other inputs). The closure should return `true` if the rule
passes, or `false` if it does not.

```php
<?php
$filter = $form->getFilter();

$filter->setRule(
    'first_name',
    'First name must be alphabetic only.',
    function ($value) {
        return ctype_alpha($value);
    }
);

$filter->setRule(
    'last_name',
    'Last name must be alphabetic only.',
    function ($value) {
        return ctype_alpha($value);
    }
);

// note that this rule compares the value to that of another field
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
    function ($value) {
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
    function ($value) {
        return ctype_digit($value)
            && $value >= 00000
            && $value <= 99999;
    }
);

$filter->setRule(
    'phone_type',
    'Phone type not recognized.',
    function ($value) {
        $types = ['cell', 'home', 'work'];
        return in_array($value, $types);
    }
);

$filter->setRule(
    'birthday',
    'Birthday is not a valid date.',
    function ($value) {
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
?>
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
?>
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
        $this->setField('first_name');
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
?>
```

Now when we instantiate the `ContactForm` the inputs and filters will be there
automatically.


Applying CSRF Protections
-------------------------

Aura.Input comes with an interface for implementations that prevent
[cross-site request forgery](https://www.owasp.org/index.php/Cross-Site_Request_Forgery)
attacks.  To make use of this interface, we will need to provide our own
CSRF implementation; this is because it depends on two things that Aura.Input
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
        $fieldset->setField('__csrf_token', 'hidden')
            ->setAttribs(['value' => $this->csrf->getValue()]);
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
?>
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
?>
```

Calling `setAntiCsrf()` adds a CSRF field to the form.

When we call `fill()` on the form, it will check the CSRF value in the data
to make sure it is correct.  If not, the form will not fill in the data, and
throw an exception and will not fill in the data.


Providing "Hints" To The View Layer
-----------------------------------

The Aura.Input package only describes the user inputs and their values. It
does not render forms or fields; that task is for the view layer. However,
Aura.Input does allow for "hints" that the view layer can use for rendering.

When defining a field, we can set the type as the second parameter to the
`setField()` method. This can be an HTML input type, an HTML tag name, a
custom name that the view layer recognizes, or anything else; recall that
these are only hints for the view, and are not strict. In addition, we can use
fluent methods to set attributes and options on the field.

```php
<?php
// hint the view layer to treat the first_name field as a text input,
// with size and maxlength attributes
$form->setField('first_name', 'text')
     ->setAttribs([
        'size' => 20,
        'maxlength' => 20,
     ]);

// hint the view layer to treat the state field as a select, with a 
// particular set of options (the keys are the option values, and the values
// are the displayed text)
$form->setField('state', 'select')
     ->setOptions([
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        // ...
     ]);
?>
```

In our view layer, we can extract the hints for a field using the `get()`
method.

```php
<?php
// get the hints for the state field
$hints = $form->get('state');

// the hints array looks like this:
// $hints = [
//     'type' => 'select',      # the input type
//     'name' => 'state',       # the input name
//     'attribs' => [           # attributes as key-value pairs
//         // ...
//     ],
//     'options' => [           # options as key-value pairs
//         'AL' => 'Alabama',
//         'AZ' => 'Arizona',
//         // ...
//     ],
//     'value' => '',           # the current value of the input
// ];
?>
```

The [Aura.View](http://github.com/auraphp/Aura.View) package comes with a
series of helpers that can translate the hints array to HTML.


Passing Options Into Forms
--------------------------

Frequently, the application using the inputs will have a standard set of
options used across all forms and filters. It would be inconvenient to have
to duplicate those standard options for each different form, so Aura.Input
allows us to pass in any object at all as a container for application-wide
options.  We can then use those options for building the inputs.

For example, we would construct our `ContactForm` with an arbitrary options
object ...

```php
<?php
use Aura\Input\Builder;
use Aura\Input\Filter;
use Vendor\Package\ContactForm;
use Vendor\Package\Options;

$options = new Options;
$form = new ContactForm(new Builder, new Filter, $options);
?>
```

... and then use it in the `init()` method:

```php
<?php
namespace Vendor\Package;

use Aura\Input\Form;

class ContactForm extends Form
{
    protected function init()
    {
        // the options object injected via constructor
        $options = $this->getOptions();
        
        // set input fields
        $this->setField('state', 'select')
             ->setOptions($options->getStates());
        
        // set input filters
        $filter = $this->getFilter();
        $filter->setRule(
            'state',
            'State not recognized.',
            function ($value) use ($options) {
                return in_array($value, $options->getStates());
            }
        );
    }
}
?>
```


Creating Reusable Fieldsets
---------------------------

TBD.


Using Fieldset Collections
--------------------------

TBD.

