<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Field\Cleanup;

class NoCleanup implements FieldCleanupInterface
{
    public function cleanupField(string $field, array $option): string
    {
        return $field;
    }
}
