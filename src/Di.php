<?php
declare(strict_types=1);

namespace corbomite\di;

use Exception;
use DI\Container;
use DI\ContainerBuilder;
use Composer\Console\Application;

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
                ->addDefinitions(self::getDiConfig())
                ->build();
        } catch (Exception $e) {
            $msg = 'Unable to build Dependency Injection Container';
            throw new DiException($msg, 500, $e);
        }

        return self::$diContainer;
    }

    /**
     * @throws Exception
     */
    private static function getDiConfig(): array
    {
        if (! defined('APP_BASE_PATH')) {
            throw new Exception('APP_BASE_PATH must be defined');
        }

        // Edge case and weirdness with composer
        getenv('HOME') || putenv('HOME=' . __DIR__);

        $oldCwd = getcwd();

        $diConfig = [];

        chdir(APP_BASE_PATH);

        $appJsonPath = APP_BASE_PATH . DIRECTORY_SEPARATOR . 'composer.json';

        if (file_exists($appJsonPath)) {
            $appJson = json_decode(file_get_contents($appJsonPath), true);
            $configFilePath = $appJson['extra']['diConfigFilePath'] ?? null;

            if ($configFilePath &&
                file_exists($configFilePath)
            ) {
                $configInclude = include APP_BASE_PATH . '/' . $configFilePath;
                $diConfig = \is_array($configInclude) ? $configInclude : [];
            }
        }

        $composerApp = new Application();

        /** @noinspection PhpUnhandledExceptionInspection */
        $composer = $composerApp->getComposer();
        $repositoryManager = $composer->getRepositoryManager();
        $installedFilesystemRepository = $repositoryManager->getLocalRepository();
        $packages = $installedFilesystemRepository->getCanonicalPackages();

        foreach ($packages as $package) {
            $extra = $package->getExtra();

            $configFilePath = APP_BASE_PATH .
                '/vendor/' .
                $package->getName() .
                '/' .
                ($extra['diConfigFilePath'] ?? 'asdf');

            if (file_exists($configFilePath)) {
                $configInclude = include $configFilePath;
                $config = \is_array($configInclude) ? $configInclude : [];
                $diConfig = array_merge($diConfig, $config);
            }
        }

        chdir($oldCwd);

        return $diConfig;
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
