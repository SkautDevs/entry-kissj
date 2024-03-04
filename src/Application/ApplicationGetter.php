<?php

namespace EntryKissj\Application;

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Slim\App;
use Slim\Views\Twig;
use Twig\Extension\DebugExtension;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

class ApplicationGetter
{
    public function getApp(): App
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($this->getContainerDefinition());

        $container = $containerBuilder->build();
        $app = Bridge::create($container);

        $app = $this->addRoutes($app);
        $app = $this->addMiddlewares($app);

        return $app;
    }

    /**
     * @return array<string,mixed>
     */
    private function getContainerDefinition(): array
    {
        $container = [];
        $container[Client::class] = new Client([
            'base_uri' => 'https://staging.kissj.net/',
            'timeout'  => 5,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ',
            ]
        ]);
        $container[Twig::class] = function (): Twig {
            $twig = Twig::create([
                __DIR__ . '/../Templates',
            ]);
            $twig->addExtension(new DebugExtension());

            return $twig;
        };

        return $container;
    }

    private function addRoutes(App $app): App
    {
        $app->get('/', Controller::class . '::list')
            ->setName('list');

        return $app;
    }

    private function addMiddlewares(App $app): App
    {
        $app->add(new WhoopsMiddleware());

        return $app;
    }
}
