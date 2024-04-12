<?php

declare(strict_types=1);

namespace EntryKissj\Application;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as ResponseHandler;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

class HeaderAuthorizationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, ResponseHandler $handler): Response
    {
        $bearerTokenFromSession = $_SESSION['bearerToken'] ?? null;
        if ($bearerTokenFromSession === null) {
            return $this->createRedirectResponse($request, 'showBearerTokenInput');
        }

        $request = $request->withAttribute('bearerToken', $bearerTokenFromSession);

        return $handler->handle($request);
    }

    protected function createRedirectResponse(Request $request, string $routeName): Response
    {
        $parameters = [];
        $url = $this->getRouter($request)->urlFor($routeName, $parameters);

        return (new \Slim\Psr7\Response())->withHeader('Location', $url)->withStatus(302);
    }

    protected function getRouter(Request $request): RouteParserInterface
    {
        return RouteContext::fromRequest($request)->getRouteParser();
    }
}
