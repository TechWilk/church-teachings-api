<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Field\Cleanup;

use Exception;

class RegexCleanup implements FieldCleanupInterface
{
    public function cleanupField(string $field, array $option): string
    {
        $cleanedField = preg_replace(reset($option), '$1', $field);

        if (is_null($cleanedField)) {
            throw new Exception('Failed to cleanup with regex');
        }

        return trim($cleanedField);
    }
}
