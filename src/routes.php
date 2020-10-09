<?php

namespace TechWilk\Church\Teachings;

use TechWilk\Church\Teachings\Controller\FeedController;

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->group('/feed', function() {
    $this->get('/{name}', FeedController::class . ':getExistingFeed');
    $this->post('', FeedController::class . ':postCreateFeed');
});
