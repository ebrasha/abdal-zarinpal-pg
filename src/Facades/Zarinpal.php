<?php

namespace Abdal\AbdalZarinpalPg\Facades;

use Illuminate\Support\Facades\Facade;

class Zarinpal extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zarinpal';
    }
}
