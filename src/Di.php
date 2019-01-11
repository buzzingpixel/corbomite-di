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
     * @throws DiException
     */
    public static function diContainer(): Container
    {
        if (self::$diContainer) {
            return self::$diContainer;
        }

        try {
            self::$diContainer = (new ContainerBuilder())
                ->useAutowiring(false)
                ->useAnnotations(false)
                ->addDefinitions(
                    Factory::collector()->collect('diConfigFilePath')
                )
                ->build();
        } catch (Throwable $e) {
            $msg = 'Unable to build Dependency Injection Container';
            throw new DiException($msg, 500, $e);
        }

        return self::$diContainer;
    }

    /**
     * @throws DiException
     */
    public function getDiContainer(): Container
    {
        return self::diContainer();
    }

    /**
     * Resolves a dependency (if a dependency has already been resolved, then
     * that same instance of the dependency will be returned)
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
     * @throws DiException
     */
    public function getFromDefinition(string $def)
    {
        return self::get($def);
    }

    /**
     * Resolves a dependency with a new instance of that dependency every time
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
     * @throws DiException
     */
    public function makeFromDefinition(string $def)
    {
        return self::make($def);
    }

    /**
     * Checks if the DI has a dependency definition
     * @throws DiException
     */
    public static function has(string $def)
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
     * @throws DiException
     */
    public function hasDefinition(string $def)
    {
        return self::has($def);
    }
}
