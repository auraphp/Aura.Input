# 3.0.0-alpha

* License has been changed from BSD to MIT.
* Removed `aura/installer-default` from `composer.json`.
* Removed Aura.Di configuration files.
* Added `aura/filter-interface` to `composer.json`.
* Added `Filter::addRule`, now accepts multiple rules for a single field.
* Renamed `Filter::values` to `Filter::apply`.
* Added `Filter::getFailures` which return  `Aura\Filter_Interface\FailureCollectionInterface`.
* Even if there are breaking changes the core functionality is not altered. Expecting a smooth upgrade from 1.x to 3.x.
