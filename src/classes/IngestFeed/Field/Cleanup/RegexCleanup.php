<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Field\Cleanup;

use Exception;

class RegexCleanup implements FieldCleanupInterface
{
    public function cleanupField(string $field, array $option): string
    {
        preg_match(reset($option), $field, $matches);

        $cleanedField = $matches[1] ?? '';

        return trim($cleanedField);
    }
}
