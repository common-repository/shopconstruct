<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc192ba776014a890e28496b0aa7e217d
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'ShopCT\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ShopCT\\' => 
        array (
            0 => __DIR__ . '/../..' . '/com',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc192ba776014a890e28496b0aa7e217d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc192ba776014a890e28496b0aa7e217d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
