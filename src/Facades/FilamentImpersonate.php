<?php

namespace Dearvn\FilamentImpersonate\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dearvn\FilamentImpersonate\FilamentImpersonate
 */
class FilamentImpersonate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Dearvn\FilamentImpersonate\FilamentImpersonate::class;
    }
}
