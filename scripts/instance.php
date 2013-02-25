<?php
namespace Aura\Form;
require_once dirname(__DIR__) . '/src.php';
return new Form(new FieldCollection(new FieldBuilder), new Options, new Filter);
