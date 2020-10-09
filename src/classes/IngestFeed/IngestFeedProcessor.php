<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed;

use TechWilk\Church\Teachings\IngestFeed\Feed\Fetcher\FeedFetcherInterface;
use TechWilk\Church\Teachings\IngestFeed\Feed\Parser\FeedParserInterface;
use TechWilk\Church\Teachings\IngestFeed\Field\Cleanup\FieldCleanupInterface;
use TechWilk\Church\Teachings\IngestFeed\Field\Validator\FieldValidatorInterface;
use TechWilk\Church\Teachings\IngestFeed\Field\Validator\InvalidFieldException;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Container\ContainerInterface;

class IngestFeedProcessor
{
    protected $container;
    protected $feedsTable;
    protected $feedMappingsTable;

    public function __construct(
        ContainerInterface $container, 
        Builder $feedsTable, 
        Builder $feedMappingsTable
    ) {
        $this->container = $container;
        $this->feedsTable = $feedsTable;
        $this->feedMappingsTable = $feedMappingsTable;
    }

    public function processFeeds()
    {
        $feeds = $this->feedsTable->get();

        foreach ($feeds as $feed) {
            $this->processFeed($feed->id);
        }
    }

    protected function processFeed(int $feedId)
    {
        // DB::enableQueryLog();
        // $feedData = $this->feedsTable->find($feedId);
        $feedsTable = clone $this->feedsTable;
        $feedData = $feedsTable->find($feedId);

        $feedFetcher = $this->getFeedFetcher($feedData->fetcher_type);
        $feedParser = $this->getFeedParser($feedData->parser_type);

        $feedString = $feedFetcher->fetchFeed($feedData->location);

        $feedMappingsTable = clone $this->feedMappingsTable;
        $feedMappingsFromDb = $feedMappingsTable->where('feed_id', '=', $feedId)->get();
        // var_dump(DB::getQueryLog());

        $mappings = [];
        $mappingSelectors = [];
        foreach ($feedMappingsFromDb as $row) {
            $mappings[$row->field] = $row;
            $mappingSelectors[$row->field] = $row->selector;
        }

        $parsedFeed = $feedParser->parseFeed($feedString, $mappingSelectors);

        $feedContents = [];
        foreach ($parsedFeed as $key => $parsedItem) {
            try {
                $feedItemContents = [];
                foreach ($parsedItem as $fieldName => $parsedField) {
                    if (empty($mappings[$fieldName])) {
                        continue;
                    }

                    $fieldConfig = $mappings[$fieldName];

                    $fieldCleanup = $this->getFieldCleanup($fieldConfig->cleaner);
                    $cleanedField = $fieldCleanup->cleanupField($parsedField, json_decode($fieldConfig->cleaner_config, true));

                    $fieldValidator = $this->getFieldValidator($fieldConfig->validator);

                    if (!$fieldValidator->validateField($cleanedField, json_decode($fieldConfig->validator_config, true))) {
                        echo 'Failing #' . $key . ' due to "' . $fieldName . '" containing invalid data "' . $cleanedField . '"' . PHP_EOL;
                        continue 2;
                    }
                    $feedItemContents[$fieldName] = $cleanedField;
                }
                $feedContents[$key] = $feedItemContents;

            } catch (InvalidFieldException $e) {
            }

            // persist item to data storage



        }

        var_dump($feedContents);
        
    }

    protected function getFeedFetcher(string $className): FeedFetcherInterface
    {
        /** @var FeedFetcherInterface */
        $feedFetcher = $this->container->get($className);

        return $feedFetcher;
    }

    protected function getFeedParser(string $className): FeedParserInterface
    {
        /** @var FeedParserInterface */
        $feedParser = $this->container->get($className);

        return $feedParser;
    }

    protected function getFieldCleanup(string $className): FieldCleanupInterface
    {
        /** @var FieldCleanupInterface */
        $fieldCleanup = $this->container->get($className);

        return $fieldCleanup;
    }

    protected function getFieldValidator(string $className): FieldValidatorInterface
    {
        /** @var FieldValidatorInterface */
        $fieldValidator = $this->container->get($className);

        return $fieldValidator;
    }
}