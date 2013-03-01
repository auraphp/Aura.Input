Aura.Input
==========

This package contains tools to describe the fields and values in an HTML form.
Note that this package does not include filtering or output functionality
(although same can be added by end-users).

This package is compliant with [PSR-0][], [PSR-1][], and [PSR-2][]. If you
notice compliance oversights, please send a patch via pull request.

[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

Getting Started
===============

The easiest way to instantiate a new input (i.e., a new `Form`) 
is to include the `instance.php` script:

```php
<?php
$form = require "/path/to/Aura.Input/scripts/instance.php";
```

Alternatively, we can add the `Aura.Input` package to an autoloader, and
instantiate manually:

```php
<?php
use Aura\Input\Filter;
use Aura\Input\Form;
use Aura\Input\FieldCollection;
use Aura\Input\FieldBuilder;
use Aura\Input\Options;

$form = new Form(new FieldCollection(new FieldBuilder), new Options, new Filter);
```

Setting Field
=============

The `setField` method of the `Aura\Input\Form` create a `Aura\Input\Field`
object. By default the second paramter is text. You can also pass the different
input types like `checkbox`, 'radio', `textarea` etc.

```php
<?php
$field = $form->setField('fieldname', 'type');
```

The `setField` returns an object of `Aura\Input\Field`. We can set the 
attributes, options to the field via `attribs` and `options` method.

Alternatively you can set the fields inside the `init` method of the 
class which extends the `Aura\Input\Form`.

```php
<?php
namespace Vendor\Package;

use Aura\Input\Form

class ContactForm extends Form
{
    public function init()
    {
        $field = $this->setField('field-name', 'type');
        // set attributes, options
        // $field->attribs(array(....))
        // $field->options(array(....))

        // More fields here
    }
}
```

Getting Field
=============

We can get the whole attributes, value, option of a field via `getField`
method. Note that this is an array and not the Field object.

```php
<?php
$form->getField('fieldname');
```

Setting Values
==============
You can set the values to the fields of a `Form` object via `setValues()`.

```php
<?php
$data = ['key' => 'value', ]; 
//Eg : ['name' => 'Paul M Jones', 'email' => 'hello@example.com'];
$form->setValues($data);
```

The `Aura.Input` has a base filter class which you can pass closure as the rules.
But you are not limited, you can always use [Aura.Filter][] or some other 
validating and filtering packages. Also the Aura.Input does not have 
rendering functionality.

Making use of Aura.Filter
=========================

To make use of [Aura.Filter][] in `Aura.Input`, we need to extend the 
`Aura\Filter\RuleCollection` object and implement the `Aura\Input\FilterInterface`

```php
<?php
namespace Vendor\Package;

use Aura\Filter\RuleCollection;
use Aura\Input\FilterInterface;

class Filter extends RuleCollection implements FilterInterface
{
}
```

Now we can create the object of the `Vendor\Package\Filter` which accepts 
the same parameters as of `Aura\Filter\RuleCollection`.

Let us create a `ContactForm` and validate it.

```php
<?php
namespace Vendor\Package;

use Aura\Framework\Input\Form as InputForm;

class ContactForm extends InputForm
{
    public function init()
    {
        $name    = $this->setField('name');
        $email   = $this->setField('email');
        $url     = $this->setField('url');
        $message = $this->setField('message', 'textarea');
        
        // Get the filter and set rules
        
        $filter = $this->getFilter();
        $filter->addSoftRule('name', $filter::IS, 'string');
        $filter->addSoftRule('email', $filter::IS, 'email');
        $filter->addSoftRule('url', $filter::IS, 'url');
        $filter->addSoftRule('message', $filter::FIX, 'string');
        $filter->addSoftRule('message', $filter::FIX, 'strlenMin', 6);
    }
}
```

The `init()` method adds the fields to the input. The `setField` returns 
`Aura\Input\Field` object. You can add the attributes and options via `attribs`
and `options` method on `Aura\Input\Field` object.

```php
<?php
//create a filter object
$filter = new Vendor\Package\Filter(
    new RuleLocator(array_merge(
        require 'path/to/Aura.Filter/scripts/registry.php',
        ['any' => function () {
            $rule = new \Aura\Filter\Rule\Any;
            $rule->setRuleLocator(new \Aura\Filter\RuleLocator(
                require 'path/to/Aura.Filter/scripts/registry.php'
            ));
            return $rule;
        }]
    )),
    new Translator(require 'path/to/Aura.Filter/scripts/intl/en_US.php')
);

$form = new Vendor\Package\ContactForm(
    new FieldCollection(new FieldBuilder),
    new Options(),
    $filter
);
```

Please visit [Aura.Filter][] for more information on manual instantiation, 
validating and filtering.

Setting Values
==============

You can use `setValues` on the form object.

```php
$form->setValues($data);
```

Validate, Filter and Getting Error Message
==========================================
In the above example we can filter and validate the fields and get the
error messages via `getMessages` method. It accepts null and field name.
If you pass the fieldname it will be giving the error message of the 
field only.

```php
<?php
if (! $form->filter()) {
    $messages = $form->getMessages();
}
```

Rendering : via Aura.View
=========================

As `Aura.Input` doesn't have a rendering functionality we can make use 
of [Aura.View][] or similar ones. The [Aura.View][] has built in capability
of rendering the field attributes and values.

```php
<?php
// We assume you have passed the form object to the view
$field = $form->getField('fieldname');
// from the template
echo $this->field($field);
```

For more information visit [Aura.View][]

[Aura.Di]: https://github.com/auraphp/Aura.Di
[Aura.Filter]: https://github.com/auraphp/Aura.Filter
[Aura.View]: https://github.com/auraphp/Aura.View
