<?php
namespace Aura\Input;
require_once dirname(__DIR__) . '/src.php';
return new Form(new FieldCollection(new FieldFactory), new Options, new Filter);
