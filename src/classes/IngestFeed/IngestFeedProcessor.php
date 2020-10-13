<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed;

use DateTimeImmutable;
use Exception;
use TechWilk\Church\Teachings\IngestFeed\Feed\Fetcher\FeedFetcherInterface;
use TechWilk\Church\Teachings\IngestFeed\Feed\Parser\FeedParserInterface;
use TechWilk\Church\Teachings\IngestFeed\Field\Cleanup\FieldCleanupInterface;
use TechWilk\Church\Teachings\IngestFeed\Field\Validator\FieldValidatorInterface;
use TechWilk\Church\Teachings\IngestFeed\Field\Validator\InvalidFieldException;
use Illuminate\Database\Query\Builder;
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
        $organiserId = $feedData->organiser_id;

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
                        throw new InvalidFieldException('"' . $fieldName . '" containing invalid data "' . $cleanedField . '"');
                    }
                    $feedItemContents[$fieldName] = $cleanedField;
                }
                $feedContents[$key] = $feedItemContents;

            } catch (InvalidFieldException $e) {
            }

            // persist item to data storage



        }

        var_dump($feedContents);

        $this->persistItemsToStorage($organiserId, $feedContents);
    }

    protected function persistItemsToStorage(int $organiserId, array $feedContents): void
    {
        $teachingsTable = $this->container->get('db')->table('teachings');
        $passagesTable = $this->container->get('db')->table('teaching_passages');

        foreach ($feedContents as $item) {
            $speakerId = $this->findSpeakerIdFromName($item['speaker']);
            $seriesId = $this->findSeriesIdFromName($organiserId, $item['series']);

            $teachingId = $teachingsTable->insertGetId([
                'dedupe_id' => $item['dedupe'],
                'dedupe_hash' => hash('sha3-512', $item['dedupe']),
                'name' => $item['title'],
                'slug' => $this->slugify($item['title']),
                'date' => $this->parseDate($item['date'])->format('Y-m-d H:i:s'),
                'file_hash' => '',
                'file_url' => $item['file'] ?? '',
                'organiser_id' => $organiserId,
                'speaker_id' => $speakerId,
                'series_id' => $seriesId,
                'description' => $item['description'] ?? '',
                'duration' => 0,
                'url' => $item['url'],
            ]);

            $passages = $this->parseVerses($item['verses']);
            foreach ($passages as $passage) {
                $passagesTable->insert([
                    'teaching_id' => $teachingId,
                    'passage' => $passage,
                ]);
            }
        }
        exit;
    }

    protected function parseVerses(string $verses): array
    {
        return [$verses];
    }

    protected function parseDate(string $dateString): DateTimeImmutable
    {
        $date = null;
        try {
            $date = new DateTimeImmutable($dateString);
        } catch (Exception $e) {

        }

        if (!$date instanceof DateTimeImmutable) {
            $date = DateTimeImmutable::createFromFormat('d/m/Y', $dateString);
        }

        if (!$date instanceof DateTimeImmutable) {
            $date = DateTimeImmutable::createFromFormat('dmy', $dateString);
        }

        return $date;
    }

    protected function slugify(string $name): string
    {
        $name = preg_replace('/[^a-zA-Z0-9]/', '-', $name);
        $name = preg_replace('/\-{2}/', '-', $name);
        $name = strtolower($name);

        return $name;
    }

    protected $speakersCache = [];
    protected function findSpeakerIdFromName(string $name): int
    {
        if (array_key_exists($name, $this->speakersCache)) {
            return $this->speakersCache[$name];
        }

        $speakersTable = $this->container->get('db')->table('speakers');
        $speaker = $speakersTable->where('full_name', '=', $name)->first();
        if ($speaker) {
            $this->speakersCache[$name] = $speaker->id;
            
            return $speaker->id;
        }

        $speakerId = $speakersTable->insertGetId(
            [
                'full_name' => $name,
                'known_name' => $name, 
                'description' => '', 
                'image_hash' => '',
            ]
        );

        $this->speakersCache[$name] = $speakerId;
            
        return $speakerId;
    }

    protected $seriesCache = [];
    protected function findSeriesIdFromName(int $organiserId, string $name): int
    {
        if (array_key_exists($name, $this->seriesCache)) {
            return $this->seriesCache[$organiserId . $name];
        }

        $seriesTable = $this->container->get('db')->table('teaching_series');
        $series = $seriesTable->where('organiser_id', '=', $organiserId)->where('name', '=', $name)->first();
        if ($series) {
            $this->seriesCache[$organiserId . $name] = $series->id;

            return $series->id;
        }

        $seriesId = $seriesTable->insertGetId(
            [
                'organiser_id' => $organiserId,
                'name' => $name,
                'description' => '',
            ]
        );

        $this->seriesCache[$organiserId . $name] = $seriesId;

        return $seriesId;
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