<?php

namespace Wenhsing\UrlSign\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Wenhsing\UrlSign\UrlSignManager;

class UrlSignMiddleware
{
    protected $urlSign;

    public function __construct(UrlSignManager $urlSign)
    {
        $this->urlSign = $urlSign;
    }

    public function handle($request, Closure $next)
    {
        if ($this->urlSign->verify($request->url(), $request->query())) {
            return $next($request);
        }
        return $this->errorResponse($request, $next);
    }

    public function errorResponse($request, $next)
    {
        throw new HttpException(401, 'Signature verification failed.');
    }
}
