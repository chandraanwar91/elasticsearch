<?php

namespace Chandraanwar91\Elasticsearch\Facades;

use Illuminate\Support\Facades\Facade;

class ES extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'es';
    }
}