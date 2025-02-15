<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitabc0a3d526dde70d2dcbbc493b08aceb
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitabc0a3d526dde70d2dcbbc493b08aceb', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitabc0a3d526dde70d2dcbbc493b08aceb', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitabc0a3d526dde70d2dcbbc493b08aceb::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
