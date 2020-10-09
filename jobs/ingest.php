<?php

use TechWilk\Church\Teachings\IngestFeed\IngestFeedProcessor;

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

require __DIR__ . '/../src/dependencies.php';

$feedsTable = $container->get('db')->table('ingest_feeds');
$feedMappingsTable = $container->get('db')->table('ingest_feed_mappings');

$ingestFeedProcessor = new IngestFeedProcessor($container, $feedsTable, $feedMappingsTable);

$ingestFeedProcessor->processFeeds();
