<?php
// DIC configuration

use League\HTMLToMarkdown\HtmlConverter;
use TechWilk\Church\Teachings\IngestFeed\Feed\Fetcher\ScraperFeedFetcher;
use TechWilk\Church\Teachings\IngestFeed\Feed\Parser\HtmlParser;
use TechWilk\Church\Teachings\IngestFeed\Feed\Parser\RssParser;
use TechWilk\Church\Teachings\IngestFeed\Field\Cleanup\HtmlToMarkdownCleanup;
use TechWilk\Church\Teachings\IngestFeed\Field\Cleanup\StringReplaceCleanup;
use TechWilk\Church\Teachings\IngestFeed\Field\Cleanup\NoCleanup;
use TechWilk\Church\Teachings\IngestFeed\Field\Cleanup\RegexCleanup;
use TechWilk\Church\Teachings\IngestFeed\Field\Validator\NoValidator;
use TechWilk\Church\Teachings\IngestFeed\Field\Validator\PresenceValidator;

$container = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// elequent
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {

    return $capsule;
};

$container[ScraperFeedFetcher::class] = function ($container) {
    return new ScraperFeedFetcher(new GuzzleHttp\Client());
};

$container[HtmlParser::class] = function ($container) {
    return new HtmlParser();
};

$container[RssParser::class] = function ($container) {
    return new RssParser();
};

$container[NoCleanup::class] = function ($container) {
    return new NoCleanup();
};

$container[PresenceValidator::class] = function ($container) {
    return new PresenceValidator();
};

$container[NoValidator::class] = function ($container) {
    return new NoValidator();
};

$container[StringReplaceCleanup::class] = function ($container) {
    return new StringReplaceCleanup();
};

$container[RegexCleanup::class] = function ($container) {
    return new RegexCleanup();
};

$container[HtmlToMarkdownCleanup::class] = function ($container) {
    $converter = new HtmlConverter([
        'strip_tags' => true,
    ]);

    return new HtmlToMarkdownCleanup($converter);
};
