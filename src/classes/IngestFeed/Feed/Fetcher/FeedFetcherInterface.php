<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Feed\Fetcher;

interface FeedFetcherInterface
{
    public function fetchFeed(string $location): string;
}