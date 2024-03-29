<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Feed\Parser;

use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class RssParser implements FeedParserInterface
{
    public function parseFeed(
        string $contents, 
        string $itemSelector, 
        array $mappingSelectors
    ): array {
        $crawler = new class extends Crawler {
            public function getCombinedText()
            {
                $text = '';
                foreach ($this as $node) {
                    /** @var \DOMNode $node */
                    $text .= $node->nodeValue;
                }
                return $text;
            }
        };

        $crawler->addXmlContent($contents);

        $itemNodes = $crawler->filter($itemSelector);

        $data = $itemNodes->each(function ($node, $i) use ($mappingSelectors) {
            $fieldsFound = false;
            $data = [];
            foreach ($mappingSelectors as $mappedField => $selector) {
                try {
                    if (']' === substr($selector, -1)) {
                        preg_match('/\[([^\]]*)\]$/', $selector, $matches);
                        $data[$mappedField] = trim($node->filter($selector)->attr($matches[1]));
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
