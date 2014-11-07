<?php namespace mrkmg\NativeSass\Facades;

use Illuminate\Support\Facades\Facade;

class NativeSass extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mrkmg.nativesass';
    }
}
