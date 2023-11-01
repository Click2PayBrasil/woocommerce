<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit27fa1d7c2c7118a26c8e02e51b7aba7b
{
    public static $files = array (
        '9971e2325adba55b8bc9e5143ef0711a' => __DIR__ . '/../..' . '/includes/core-functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Click2pay_Payments\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Click2pay_Payments\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit27fa1d7c2c7118a26c8e02e51b7aba7b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit27fa1d7c2c7118a26c8e02e51b7aba7b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit27fa1d7c2c7118a26c8e02e51b7aba7b::$classMap;

        }, null, ClassLoader::class);
    }
}
