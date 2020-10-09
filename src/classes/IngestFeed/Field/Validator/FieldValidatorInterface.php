<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Field\Validator;

interface FieldValidatorInterface
{
    public function validateField(string $fieldData, array $config): bool;
}
