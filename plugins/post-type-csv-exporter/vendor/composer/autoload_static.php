<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit02967eab6fbf39623934fa5f93402165
{
    public static $files = array (
        'f13b5304cc5960f6dd19fccb3ef39750' => __DIR__ . '/../../../..'.'/build' . '/../plugins/post-type-csv-exporter/source/php/Function/AutoLoad.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PostTypeCSVExporter\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PostTypeCSVExporter\\' => 
        array (
            0 => __DIR__ . '/../../../..'.'/build' . '/../plugins/post-type-csv-exporter/source/php',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit02967eab6fbf39623934fa5f93402165::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit02967eab6fbf39623934fa5f93402165::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit02967eab6fbf39623934fa5f93402165::$classMap;

        }, null, ClassLoader::class);
    }
}
