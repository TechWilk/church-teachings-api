<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Field\Cleanup;

class StringReplaceCleanup implements FieldCleanupInterface
{
    public function cleanupField(string $field, array $option): string
    {
        $cleanedField = str_replace($option, '', $field);

        return trim($cleanedField);
    }
}
