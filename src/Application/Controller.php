<?php

declare(strict_types=1);

namespace EntryKissj\Application;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class Controller
{
    public const URL_KISSJ = 'v3/entry/list';
    public const BEARER_TOKEN_SESSION = 'bearerToken';
    private const BEARER_TOKEN_FORM = 'bearerToken';

    public function showBearerTokenInput(
        Response $response,
        Twig $twig,
    ): Response {
        return $twig->render(
            $response,
            'setBearerToken.twig',
        );
    }

    public function setBearerToken(
        Request $request,
        Response $response,
    ): Response {
        /** @var array<mixed> $parsedBody */
        $parsedBody = $request->getParsedBody();
        if (array_key_exists('bearerToken', $parsedBody) === false) {
            throw new RuntimeException('Bearer token is missing');
        }
        $_SESSION[self::BEARER_TOKEN_SESSION] = $parsedBody[self::BEARER_TOKEN_FORM];

        return $this->getRedirectResponse($request, $response, 'list');
    }

    public function list(
        Request $request,
        Response $response,
        Client $httpClient,
        Twig $twig,
    ): Response {
        try {
            $httpResponse = $httpClient->get(self::URL_KISSJ);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                return $this->getRedirectResponse($request, $response, 'showBearerTokenInput');
            }

            throw $e;
        }
        $body = (string)$httpResponse->getBody();
        $jsonDecode = json_decode($body, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);

        return $twig->render(
            $response,
            'main.twig',
            ['list' => $jsonDecode],
        );
    }

    /**
     * @param array<string,string> $arguments
     */
    private function getRedirectResponse(
        Request $request,
        Response $response,
        string $routeName,
        array $arguments = [],
    ): Response {
        return $response
            ->withHeader('Location', $this->getRouter($request)->urlFor($routeName, $arguments))
            ->withStatus(302);
    }

    protected function getRouter(Request $request): RouteParserInterface
    {
        return RouteContext::fromRequest($request)->getRouteParser();
    }
}
