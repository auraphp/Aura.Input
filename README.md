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
these will provide hints to your view layer on how to present the field.)


Setting Filters On The Form
---------------------------

Aura.Input comes with a very basic filter system. Use the `getFilter()` method
to get the `Filter` object, then add rules to the filter using the `setRule()`
method.

Rules are closures that test a form input value. The first parameter is the
name of the form field to test; the second parameter is the message to use if
the rule fails; the third parameter is a closure to test the form input value.
The closure should return `true` if the rule passes, or `false` if it does
not.

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
```

(We will discuss later how to implement `FilterInterface` and use your own
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

Passing Options Into Forms
--------------------------

TBD.

Applying CSRF Protections
-------------------------

TBD.

Providing "Hints" To The View Layer
-----------------------------------

TBD.

Reusable Fieldsets (aka "Sub-Forms")
------------------------------------

TBD.

Fieldset Collections
--------------------

TBD.
