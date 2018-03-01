<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class StaticPageMiddleware
{
    /**
     * Static Page Invokable Middleware
     *
     * @param  ServerRequestInterface  $request  PSR7 request object
     * @param  ResponseInterface $response PSR7 response object
     * @param  callable          $next     Next middleware callable
     *
     * @return ResponseInterface PSR7 response object
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $response = $next($request, $response);

        if (!$request->isGet() || $response->getStatusCode() != 200) {
            return $response;
        }
        
        $comment = '<!-- static page generated ' . date('c') . ' -->';
        $content = (string)$response->getBody() . $comment;
        $uri = $request->getUri();
        $path = $uri->getBasePath() . $uri->getPath();
        if (substr($path, -1) == '/') {
            $path.= 'index.html';
        }
        $path = $_SERVER['DOCUMENT_ROOT'] . $path;
        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
        return $response;
    }
}
