<?php

namespace TechWilk\Church\Teachings;

use TechWilk\Church\Teachings\Controller\FeedController;

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->group('/feed', function() {
    $this->get('/{name}', FeedController::class . ':getExistingFeed');
    $this->post('', FeedController::class . ':postCreateFeed');
    $this->get('/{name}', FeedController::class . ':getFeed');
});
