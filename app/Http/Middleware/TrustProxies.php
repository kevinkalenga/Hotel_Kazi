<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Trust all proxies (Railway, Heroku-like, etc.)
     */
    protected $proxies = '*';

    /**
     * IMPORTANT: force Laravel to trust Railway HTTPS headers
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}