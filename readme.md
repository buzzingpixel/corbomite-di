# Corbomite DI

Part of BuzzingPixel's Corbomite project.

Provides a light wrapper around PHP-DI to make available to Corbomite. Per BuzzingPixel's preferred practice of being explicit in defining dependencies, auto wiring and annotations are disabled.

## Usage

Note: make sure `APP_BASE_PATH` is defined by your application somewhere before calling the DI.

Usage is fairly simple, the class `\corbomite\di\Di` provides static methods for getting dependencies, or non-static methods if you'd like to inject it as a `new`ed up object into your own classes.

### Configuration

As mentioned above, you are required to write the definitions to be injected. Your app, or any composer package can register a config file to load in `composer.json`s `extra` object with the key `diConfigFilePath`.

```json
{
    "name": "vendor/name",
    "extra": {
        "diConfigFilePath": "src/diConfig.php"
    }
}
```

The return of that config file should look something like this:

```php
<?php
declare(strict_types=1);

use some\Dependency;
use some\OtherDependency;
use my\namespaced\CoolClass;
use my\namespaced\AnotherCoolClass;

return [
    CoolClass::class => function () {
        return new CoolClass(new Dependency());
    },
    AnotherCoolClass::class => function () {
        return new AnotherCoolClass(
            new Dependency(),
            new OtherDependency()
        );
    },
];
```

## Di's Class Methods

### `diContainer()` (static) or `getDiContainer()` (non-static)

Gets the configured instance of PHP-DI. You probably shouldn't ever need to use this.

### `get()` (static) or `getFromDefinition()` (non-static)

Get's specified dependency. If specified dependency has already been resolved, then the previously resolved instance of the dependency will be returned.

### `make()` (static) or `makeFromDefinition` (non-static)

Get's specified dependency as a new instance of class every time (unlike the get or getFromDefinition method).

### `has()` (static) or `hasDefinition` (non-static)

Checks if the specified definition exists.

## License

Copyright 2018 BuzzingPixel, LLC

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0).

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
