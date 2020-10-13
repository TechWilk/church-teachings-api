<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Feed\Parser;

use InvalidArgumentException;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class HtmlParser implements FeedParserInterface
{
    public function parseFeed(
        string $contents, 
        string $itemSelector, 
        array $mappingSelectors
    ): array {
        $crawler = new HtmlPageCrawler($contents);

        $itemNodes = $crawler->filter($itemSelector);

        $data = $itemNodes->each(function (HtmlPageCrawler $node, $i) use ($mappingSelectors) {
            $fieldsFound = false;
            $data = [];
            foreach ($mappingSelectors as $mappedField => $selector) {
                try {
                    if (']' === substr($selector, -1)) {
                        preg_match('/\[([^\]]*)\]$/', $selector, $matches);
                        $data[$mappedField] = trim($node->filter($selector)->getAttribute($matches[1]));
                    } else {
                        $data[$mappedField] = trim($node->filter($selector)->getCombinedText());
                    }
                    if (!empty($data[$mappedField])) {
                        $fieldsFound = true;
                    }
                } catch (InvalidArgumentException $e) {
                    $data[$mappedField] = '';
                }
            }

            if (!$fieldsFound) {
                return null;
            }

            return $data;
        });

        // remove any empty items
        $data = array_filter($data);

        return $data;
    }
}
