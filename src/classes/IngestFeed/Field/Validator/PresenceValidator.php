<?php

declare(strict_types=1);

namespace TechWilk\Church\Teachings\IngestFeed\Field\Validator;

class PresenceValidator implements FieldValidatorInterface
{
    public function validateField(string $fieldData, array $config): bool
    {
        return !empty($fieldData);
    }
}
