<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Trust all proxies (Railway / Docker / Cloud)
     */
    protected $proxies = '*';

    /**
     * Laravel 12 safe configuration
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}