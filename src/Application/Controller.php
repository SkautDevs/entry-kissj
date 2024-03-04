<?php

declare(strict_types=1);

namespace EntryKissj\Application;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Controller
{
    public const URL_KISSJ = 'v3/entry/list';

    public function list(
        Response $response,
        Client $httpClient,
        Twig $twig,
    ): Response {
        $httpResponse = $httpClient->get(self::URL_KISSJ);
        $body = (string)$httpResponse->getBody();
        $jsonDecode = json_decode($body, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);

        return $twig->render(
            $response,
            'list.twig',
            ['list' => $jsonDecode],
        );
    }
}
