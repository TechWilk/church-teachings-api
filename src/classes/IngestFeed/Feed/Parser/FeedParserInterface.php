<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Feed\Parser;

interface FeedParserInterface
{
    public function parseFeed(
        string $contents, 
        string $itemSelector, 
        array $mappingSelectors
    ): array;
}
