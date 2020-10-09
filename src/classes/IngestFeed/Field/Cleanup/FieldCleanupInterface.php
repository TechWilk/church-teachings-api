<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Field\Cleanup;

interface FieldCleanupInterface
{
    public function cleanupField(string $fieldData, array $config): string;
}