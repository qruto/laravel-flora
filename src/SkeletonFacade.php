<?php

namespace ZFort\Skeleton;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ZFort\Skeleton\SkeletonClass
 */
class SkeletonFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'skeleton';
    }
}
