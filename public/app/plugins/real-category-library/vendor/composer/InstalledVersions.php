<?php

namespace DevOwl\RealCategoryLibrary\Vendor\Composer;

use DevOwl\RealCategoryLibrary\Vendor\Composer\Semver\VersionParser;
class InstalledVersions
{
    private static $installed = array('root' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(), 'reference' => '7858b281e18a5efb9e37133ae3285b9fc359e819', 'name' => '__root__'), 'versions' => array('__root__' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(), 'reference' => '7858b281e18a5efb9e37133ae3285b9fc359e819'), 'devowl-wp/freemium' => array('pretty_version' => 'dev-build/composer-installedversions', 'version' => 'dev-build/composer-installedversions', 'aliases' => array(), 'reference' => 'f46163e56a6be9eace5a48ecd0c5c8d1680219a6'), 'devowl-wp/real-product-manager-wp-client' => array('pretty_version' => 'dev-build/composer-installedversions', 'version' => 'dev-build/composer-installedversions', 'aliases' => array(), 'reference' => 'f622da0b0b9d77b5ae0db4e38614fb8acf98b897'), 'devowl-wp/real-utils' => array('pretty_version' => 'dev-build/composer-installedversions', 'version' => 'dev-build/composer-installedversions', 'aliases' => array(), 'reference' => '8f35c9a3c7cc4a086422c95356b890212cbb8c65'), 'devowl-wp/utils' => array('pretty_version' => 'dev-build/composer-installedversions', 'version' => 'dev-build/composer-installedversions', 'aliases' => array(), 'reference' => '3b2eee5934e5ce0d94cdb5f4736259d448fe8531'), 'matthiasweb/wpdb-batch' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(), 'reference' => '8558c8c07763cd01d2c89744f65da4880b4e38a0'), 'yahnis-elsts/plugin-update-checker' => array('pretty_version' => 'dev-master', 'version' => 'dev-master', 'aliases' => array(0 => '9999999-dev'), 'reference' => '3155f2d3f1ca5e7ed3f25b256f020e370515af43')));
    public static function getInstalledPackages()
    {
        return \array_keys(self::$installed['versions']);
    }
    public static function isInstalled($packageName)
    {
        return isset(self::$installed['versions'][$packageName]);
    }
    public static function satisfies(\DevOwl\RealCategoryLibrary\Vendor\Composer\Semver\VersionParser $parser, $packageName, $constraint)
    {
        $constraint = $parser->parseConstraints($constraint);
        $provided = $parser->parseConstraints(self::getVersionRanges($packageName));
        return $provided->matches($constraint);
    }
    public static function getVersionRanges($packageName)
    {
        if (!isset(self::$installed['versions'][$packageName])) {
            throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
        }
        $ranges = array();
        if (isset(self::$installed['versions'][$packageName]['pretty_version'])) {
            $ranges[] = self::$installed['versions'][$packageName]['pretty_version'];
        }
        if (\array_key_exists('aliases', self::$installed['versions'][$packageName])) {
            $ranges = \array_merge($ranges, self::$installed['versions'][$packageName]['aliases']);
        }
        if (\array_key_exists('replaced', self::$installed['versions'][$packageName])) {
            $ranges = \array_merge($ranges, self::$installed['versions'][$packageName]['replaced']);
        }
        if (\array_key_exists('provided', self::$installed['versions'][$packageName])) {
            $ranges = \array_merge($ranges, self::$installed['versions'][$packageName]['provided']);
        }
        return \implode(' || ', $ranges);
    }
    public static function getVersion($packageName)
    {
        if (!isset(self::$installed['versions'][$packageName])) {
            throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
        }
        if (!isset(self::$installed['versions'][$packageName]['version'])) {
            return null;
        }
        return self::$installed['versions'][$packageName]['version'];
    }
    public static function getPrettyVersion($packageName)
    {
        if (!isset(self::$installed['versions'][$packageName])) {
            throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
        }
        if (!isset(self::$installed['versions'][$packageName]['pretty_version'])) {
            return null;
        }
        return self::$installed['versions'][$packageName]['pretty_version'];
    }
    public static function getReference($packageName)
    {
        if (!isset(self::$installed['versions'][$packageName])) {
            throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
        }
        if (!isset(self::$installed['versions'][$packageName]['reference'])) {
            return null;
        }
        return self::$installed['versions'][$packageName]['reference'];
    }
    public static function getRootPackage()
    {
        return self::$installed['root'];
    }
    public static function getRawData()
    {
        return self::$installed;
    }
    public static function reload($data)
    {
        self::$installed = $data;
    }
}
