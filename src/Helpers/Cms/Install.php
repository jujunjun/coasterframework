<?php namespace CoasterCms\Helpers\Cms;

use File;
use Illuminate\Support\Facades\Storage;
use Route;

class Install
{

    protected static $_loadedState;

    public static function getInstallState($reload = false)
    {
        if ($reload || !isset(self::$_loadedState)) {
            static::setInstallState();
        }

        return self::$_loadedState;
    }

    public static function setInstallState($state = '')
    {
        $filePath = self::_getFilePath();
        if (!Storage::disk('local')->assertExists($filePath)) {
            $dir = pathinfo($filePath, PATHINFO_DIRNAME);
            if (!File::exists($dir)) {
                File::makeDirectory($dir);
            }
            $state = $state ?: 'coaster.install.permissions';
        }
        if ($state) {
            Storage::disk('local')->put($filePath, $state);
            static::$_loadedState = static::$_loadedState ?: $state;
        } else {
            static::$_loadedState = Storage::disk('local')->get($filePath);
        }
    }

    public static function getRedirectRoute()
    {
        $installState = self::getInstallState();
        if (!Route::getRoutes()->hasNamedRoute($installState)) {
            $installState = 'coaster.install.permissions';
        }
        return $installState;
    }

    public static function isComplete($reload = false)
    {
        return strpos(self::getInstallState($reload), 'complete') !== false;
    }

    protected static function _getFilePath()
    {
        return config('coaster::site.storage_path') . '/install.txt';
    }

}
