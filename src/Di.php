<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace corbomite\di;

use Throwable;
use DI\Container;
use ReflectionClass;
use DI\ContainerBuilder;
use Composer\Autoload\ClassLoader;
use corbomite\configcollector\Factory;

class Di
{
    /** @var Container $diContainer */
    private static $diContainer;

    /**
     * Gets the DI container. Builds DI container if it hasn't been built yet
     * @throws DiException
     */
    public static function diContainer(): Container
    {
        if (self::$diContainer) {
            return self::$diContainer;
        }

        self::build();

        return self::$diContainer;
    }

    /**
     * Gets the DI container. Builds DI container if it hasn't been built yet
     * @throws DiException
     */
    public function getDiContainer(): Container
    {
        return self::diContainer();
    }

    /**
     * Builds the container. You probably shouldn't call this during normal
     * application execution
     * @param array $definitions
     * @throws DiException
     */
    public static function build(array $definitions = []): void
    {
        try {
            $collector = Factory::collector();

            $config = $collector->getExtraKeyAsArray('corbomiteDiConfig');
            $config = \is_array($config) ? $config : [];

            $useAutoWiring = $config['useAutoWiring'] ?? getenv('CORBOMITE_DI_USE_AUTO_WIRING') === 'true';

            $useAnnotations = $config['useAnnotations'] ?? getenv('CORBOMITE_DI_USE_ANNOTATIONS') === 'true';

            $ignorePhpDocErrors = $config['ignorePhpDocErrors'] ??
                getenv('CORBOMITE_DI_IGNORE_PHPDOC_ERRORS') === 'true';

            $enableCompilation = getenv('CORBOMITE_DI_ENABLE_COMPILATION') === 'true';

            $compileTo = $config['compileTo'] ?? false;

            if ($compileTo) {
                $compileTo = self::getAppBasePath() . DIRECTORY_SEPARATOR . $compileTo;
            }

            if (! $compileTo) {
                $compileTo = getenv('CORBOMITE_DI_COMPILATION_DIR');
            }

            $enableWritingProxies = getenv('CORBOMITE_DI_ENABLE_WRITING_PROXIES') === 'true';

            $writeProxiesToFile = $config['writeProxiesTo'] ?? false;

            if ($writeProxiesToFile) {
                $writeProxiesToFile = self::getAppBasePath() . DIRECTORY_SEPARATOR . $writeProxiesToFile;
            }

            if (! $writeProxiesToFile) {
                $writeProxiesToFile = getenv('CORBOMITE_DI_WRITE_PROXIES_TO_FILE');
            }

            $definitions = array_merge(
                $collector->collect('diConfigFilePath'),
                $definitions
            );

            $builder = new ContainerBuilder();

            $builder->useAutowiring($useAutoWiring);

            $builder->useAnnotations($useAnnotations);

            $builder->ignorePhpDocErrors($ignorePhpDocErrors);

            if ($enableCompilation && $compileTo) {
                $builder->enableCompilation($compileTo);
            }

            if ($enableWritingProxies && $writeProxiesToFile) {
                $builder->writeProxiesToFile(true, $writeProxiesToFile);
            }

            $builder->addDefinitions($definitions);

            self::$diContainer = $builder->build();
        } catch (Throwable $e) {
            $msg = 'Unable to build Dependency Injection Container';
            throw new DiException($msg, 500, $e);
        }
    }

    /**
     * Builds the container. You probably shouldn't call this during normal
     * application execution
     * @param array $definitions
     * @throws DiException
     */
    public function buildContainer(array $definitions = []): void
    {
        self::build($definitions);
    }

    /**
     * Resolves a dependency (if a dependency has already been resolved, then
     * that same instance of the dependency will be returned)
     * @param string $def
     * @return mixed
     * @throws DiException
     * @deprecated Just get the PHP-DI container and use that why did I even do this?
     */
    public static function get(string $def)
    {
        try {
            return self::diContainer()->get($def);
        } catch (Throwable $e) {
            $msg = 'Unable to get dependency';
            throw new DiException($msg, 500, $e);
        }
    }

    /**
     * Resolves a dependency (if a dependency has already been resolved, then
     * that same instance of the dependency will be returned)
     * @param string $def
     * @return mixed
     * @throws DiException
     * @deprecated Just get the PHP-DI container and use that why did I even do this?
     */
    public function getFromDefinition(string $def)
    {
        return self::get($def);
    }

    /**
     * Resolves a dependency with a new instance of that dependency every time
     * @param string $def
     * @return mixed
     * @throws DiException
     * @deprecated Just get the PHP-DI container and use that why did I even do this?
     */
    public static function make(string $def)
    {
        try {
            return self::diContainer()->make($def);
        } catch (Throwable $e) {
            $msg = 'Unable to make dependency';
            throw new DiException($msg, 500, $e);
        }
    }

    /**
     * Resolves a dependency with a new instance of that dependency every time
     * @param string $def
     * @return mixed
     * @throws DiException
     * @deprecated Just get the PHP-DI container and use that why did I even do this?
     */
    public function makeFromDefinition(string $def)
    {
        return self::make($def);
    }

    /**
     * Checks if the DI has a dependency definition
     * @param string $def
     * @return bool
     * @throws DiException
     * @deprecated Just get the PHP-DI container and use that why did I even do this?
     */
    public static function has(string $def): ?bool
    {
        try {
            return self::diContainer()->has($def);
        } catch (Throwable $e) {
            $msg = 'Unable to check if container has dependency';
            throw new DiException($msg, 500, $e);
        }
    }

    /**
     * Checks if the DI has a dependency definition
     * @param string $def
     * @return bool
     * @throws DiException
     * @deprecated Just get the PHP-DI container and use that why did I even do this?
     */
    public function hasDefinition(string $def): ?bool
    {
        return self::has($def);
    }

    private static $appBasePath = '';

    private static function getAppBasePath(): string
    {
        if (self::$appBasePath) {
            return self::$appBasePath;
        }

        if (defined('APP_BASE_PATH')) {
            self::$appBasePath = APP_BASE_PATH;
            return self::$appBasePath;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $reflection = new ReflectionClass(ClassLoader::class);

        self::$appBasePath = dirname($reflection->getFileName(), 3);

        return self::$appBasePath;
    }
}
