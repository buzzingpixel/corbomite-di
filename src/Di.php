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
use DI\ContainerBuilder;
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
            $definitions = array_merge(
                Factory::collector()->collect('diConfigFilePath'),
                $definitions
            );

            self::$diContainer = (new ContainerBuilder())
                ->useAutowiring(
                    getenv('CORBOMITE_DI_USE_AUTO_WIRING') === 'true'
                )
                ->useAnnotations(
                    getenv('CORBOMITE_DI_USE_ANNOTATIONS') === 'true'
                )
                ->addDefinitions($definitions)
                ->build();
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
     */
    public function hasDefinition(string $def): ?bool
    {
        return self::has($def);
    }
}
