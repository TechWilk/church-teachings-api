<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Feed\Fetcher;

use GuzzleHttp\Client;

class ScraperFeedFetcher implements FeedFetcherInterface
{
    protected $api;

    public function __construct(Client $api)
    {
        $this->api = $api;
    }

    public function fetchFeed(string $location): string
    {
        switch($location) {
            case 'https://allsaints.church/resources/sermons':
                return file_get_contents(__DIR__.'/../../../../../tests/data/scrape.html');
            case 'https://www.stkweb.org.uk/media/allmedia.aspx':
                return file_get_contents(__DIR__ . '/../../../../../tests/data/scrape2.html');
        }
    }

    // public function fetchFeed(string $location): string
    // {
    //     $response = $this->api->get($location);

    //     return (string) $response->getBody();
    // }
}
