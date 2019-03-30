# Corbomite DI

Part of BuzzingPixel's Corbomite project.

### Configuration

Configuration can be provided in your project's `composer.json` extra key, or in some cases with environment variables.

#### `diConfigFilePath`

Your app, or any composer package can register a config file to load in `composer.json`s `extra` object with the key `diConfigFilePath`.

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

#### `corbomiteDiConfig.useAutoWiring`

`false` by default for backwards compatibility reasons. The next major version will enable this by default. Optionally set with environment variable: `putenv('CORBOMITE_DI_USE_AUTO_WIRING=true')`

#### `corbomiteDiConfig.useAnnotations`

`false` by default for backwards compatibility reasons. The next major version will enable this by default. Optionally set with environment variable: `putenv('CORBOMITE_DI_USE_ANNOTATIONS=true')`

#### `corbomiteDiConfig.ignorePhpDocErrors`

`false` by default. Optionally set with environment variable: `putenv('CORBOMITE_DI_IGNORE_PHPDOC_ERRORS=true')`

#### `corbomiteDiConfig.compileTo`

Set the path relative to your project's `composer.json`. Optionally set with environment variable (note when using environment variable this must be a full path and not relative): `putenv('CORBOMITE_DI_COMPILATION_DIR=/path/to/directory')`

#### `corbomiteDiConfig.writeProxiesTo`

Set the path relative to your project's `composer.json`. Optionally set with environment variable (note when using environment variable this must be a full path and not relative): `putenv('CORBOMITE_DI_WRITE_PROXIES_TO_FILE=/path/to/directory')`

#### `CORBOMITE_DI_ENABLE_COMPILATION` environment variable

In your production environment you'll want to set this to `true` and make sure `corbomiteDiConfig.compileTo` has a valid path.

#### `CORBOMITE_DI_ENABLE_WRITING_PROXIES` environment variable

In your production environment you'll want to set this to `true` and make sure `corbomiteDiConfig.writeProxiesTo` has a valid path.

## Di's Class Methods

### `diContainer()` (static) or `getDiContainer()` (non-static)

Gets the configured instance of PHP-DI. You probably shouldn't ever need to use this.

### `build()` (static) or `buildContainer()` (non-static)

Allows you to build the container, perhaps in your front controller, sending any additional definitions you want to directly.

## License

Copyright 2019 BuzzingPixel, LLC

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0).

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
