<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Field\Cleanup;

use League\HTMLToMarkdown\HtmlConverterInterface;

class HtmlToMarkdownCleanup implements FieldCleanupInterface
{
    protected $converter;

    public function __construct(HtmlConverterInterface $htmlConverter)
    {
        $this->converter = $htmlConverter;
    }
    public function cleanupField(string $field, array $option): string
    {
        $cleanedField = $this->converter->convert($field);

        return trim($cleanedField);
    }
}
