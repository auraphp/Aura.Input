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
use Aura\Input\Form;
use Aura\Input\FieldCollection;
use Aura\Input\FieldFactory;

$form = new Form(new FieldCollection(new FieldFactory));
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


Getting Field
=============

We can get the whole attributes, value, option of a field via `getField`
method.

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

The `Aura.Input` does not include filtering or output functionality.

To make use of filtering and validation you can make use of `[Aura.Filter][]` 
or similar ones.

Making use of Aura.Filter
=========================

To show how we can make use of [Aura.Filter][] in `Aura.Input`, let us take 
the below class from `Aura.Framework`

```php
<?php
namespace Aura\Framework\Input;

use Aura\Filter\RuleCollection;
use Aura\Input\Form as InputForm;

class Form extends InputForm
{
    protected $filter;
    
    public function setFilter(RuleCollection $filter)
    {
        $this->filter = $filter;
        $this->initFilter();
    }
    
    protected function initFilter()
    {
    }
    
    public function getFilter()
    {
        return $this->filter;
    }
    
    public function filter()
    {
        return $this->filter->values($this->values);
    }
    
    public function getMessages($field = null)
    {
        return $this->filter->getMessages($field);
    }
}
```

Now let us create a `ContactForm` 

```php
<?php
namespace Vendor\Package\Input;

use Aura\Framework\Input\Form as InputForm;

class ContactForm extends InputForm
{
    public function initFilter()
    {
        $filter = $this->getFilter();
        $filter->addSoftRule('name', $filter::IS, 'string');
        $filter->addSoftRule('email', $filter::IS, 'email');
        $filter->addSoftRule('url', $filter::IS, 'url');
        $filter->addSoftRule('message', $filter::FIX, 'string');
        $filter->addSoftRule('message', $filter::FIX, 'strlenMin', 6);
    }
    
    public function init()
    {
        $this->setField('name');
        $this->setField('email');
        $this->setField('url');
        $this->setField('message', 'textarea');
    }
}
```

The `init()` method adds the fields to the input. The `setField` returns 
`Aura\Input\Field` object. You can add the attributes and options via `attribs`
and `options` method.

Wehn we create an object of `ContactForm` we should set the `Aura\Filter\RuleCollection`
object. You can also use a dependency injection container like `Aura.Di` 
to automate this.

```php
<?php
//create a filter object
$filter = 'path/to/Aura.Filter/scripts/instance.php';

$form = new Vendor\Package\Input\ContactForm()
$form->setFilter($filter);
```

Please visit `[Aura.Filter][]` for more information on manual instantiation
and filtering and validating.

Validate, Filter and Getting Error Message
==========================================
In the above example we can filter and validate the fields and get the
error messages via `getMessages` method. It accepts null and field name.
If you pass the fieldname it will be giving the error message of the 
field only

```php
<?php
if (! $form->filter()) {
    $messages = $form->getMessages();
}
```

Rendering : via Aura.View
=========================

As `Aura.Input` doesn't have a rendering functionality we can make use 
of [Aura.View][] or similar ones. The `Aura.View` has built in capability
of rendering the field attributes and values.

```php
<?php
// We assume you have passed the form object to the view
$field = $form->getField('fieldname');
// from the template
$this->field($field);
```

For more information visit [Aura.View][]

[Aura.View]: https://github.com/auraphp/Aura.View
[Aura.Filter]: https://github.com/auraphp/Aura.Filter
