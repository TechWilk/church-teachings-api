<?php

namespace TechWilk\Church\Teachings;

use TechWilk\Church\Teachings\Controller\FeedController;
use TechWilk\Church\Teachings\Controller\TeachingController;
use TechWilk\Church\Teachings\Controller\OrganisationController;

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', function($request, $response, $args) {
    var_dump('index route fired');
    exit;
});


$app->group('/feed', function($app) {
    $app->get('/{name}', FeedController::class . ':getExistingFeed');
    $app->post('', FeedController::class . ':postCreateFeed');
});

$app->group('/teaching', function($app) {
    $app->get('s', TeachingController::class . ':getExistingTeachings');
    $app->get('/{slug}', TeachingController::class . ':getExistingTeaching');
    $app->post('', TeachingController::class . ':postCreateTeaching');
});

$app->group('/organisation', function($app) {
    $app->get('s', OrganisationController::class . ':getExistingOrganisations');
    $app->get('/{slug}', OrganisationController::class . ':getExistingOrganisation');
    $app->post('', OrganisationController::class . ':postCreateOrganisation');
});



$app->group('/series', function($app) {
    $app->get('s', SeriesController::class . ':getExistingSeries');
    $app->get('/{slug}', SeriesController::class . ':getExistingSingleSeries');
    $app->post('', SeriesController::class . ':postCreateSeries');
});


$app->group('/speaker', function($app) {
    $app->get('s', SpeakerController::class . ':getExistingSpeakers');
    $app->get('/{slug}', SpeakerController::class . ':getExistingSpeaker');
    $app->post('', SpeakerController::class . ':postCreateSpeaker');
});




$app->group('/page', function($app) {
    $app->group('/teaching', function($app) {
        $app->get('/{organisationSlug/{teachingSlug}', PageController::class . ':getSingleOrganisationTeaching');
    });
});