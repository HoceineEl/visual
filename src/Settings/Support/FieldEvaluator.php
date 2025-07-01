<?php

namespace BagistoPlus\Visual\Settings\Support;

use BagistoPlus\Visual\Settings\Base;
use Closure;

class FieldEvaluator
{
    public function __construct(
        protected array $fields = [],
        protected array $values = []
    ) {}

    public function evaluateFields(array $fields, array $values = []): array
    {
        $this->fields = $fields;
        $this->values = $values;

        $get = $this->createGetClosure();

        return collect($fields)->map(function ($field) use ($get) {
            if ($field instanceof Base) {
                return $this->evaluateField($field, $get);
            }

            return $field;
        })->toArray();
    }

    protected function evaluateField(Base $field, Closure $get): array
    {
        $fieldArray = $field->toArray();

        $fieldArray['hidden'] = $this->evaluateCondition($field->isHidden, $get);
        $fieldArray['visible'] = $this->evaluateCondition($field->isVisible, $get);

        return $fieldArray;
    }

    protected function evaluateCondition(mixed $condition, Closure $get): bool
    {
        if (is_callable($condition)) {
            return (bool) $condition($get);
        }

        return (bool) $condition;
    }

    protected function createGetClosure(): Closure
    {
        return function (string $key) {
            return $this->values[$key] ?? null;
        };
    }
}
