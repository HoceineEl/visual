<?php

namespace BagistoPlus\Visual\Settings;

use BagistoPlus\Visual\Settings\Support\FieldEvaluator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JsonSerializable;

/**
 * @phpstan-consistent-constructor
 *
 * @method $this id(string $id)
 * @method $this label(string $label)
 * @method $this type(string $type)
 * @method $this default(mixed $value)
 * @method $this info(string $info)
 */
abstract class Base implements Arrayable, JsonSerializable
{
    public string $id;

    public string $label;

    public mixed $default;

    public string $info;

    public string $type;

    public bool $isLive = false;

    /**
     * @var bool|callable
     */
    public mixed $isHidden = false;

    /**
     * @var bool|callable
     */
    public mixed $isVisible = true;

    public static string $component = 'base-setting';

    public function __construct($id, $label)
    {
        $this->id = $id;
        $this->label = $label;
    }

    public static function make(string $id, string $label = '')
    {
        $instance = (new static($id, $label ?: Str::title(str_replace('_', ' ', $id))))
            ->default(null)
            ->info('');

        $instance->type($instance->type ?? Str::snake((new \ReflectionClass(static::class))->getShortName()));

        return $instance;
    }

    public function live(bool $state = true): self
    {
        $this->isLive = $state;

        return $this;
    }

    /**
     * @param bool|callable $condition
     */
    public function hidden(mixed $condition = true): self
    {
        $this->isHidden = $condition;

        return $this;
    }

    /**
     * @param bool|callable $condition
     */
    public function visible(mixed $condition = true): self
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function evaluateWithValues(array $values = []): array
    {
        $evaluator = new FieldEvaluator([], $values);

        return $evaluator->evaluateFields([$this], $values)[0];
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        return null;
    }

    public function __call($name, $arguments)
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $arguments[0];

            return $this;
        }

        return $this;
    }

    /**
     * @param bool|callable $condition
     */
    protected function evaluateCondition(mixed $condition, ?callable $get = null): bool
    {
        if (is_callable($condition)) {
            return $condition($get);
        }

        return (bool) $condition;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type,
            'default' => $this->default,
            'info' => $this->info,
            'component' => static::$component,
            'live' => $this->isLive,
            'hidden' => $this->isHidden,
            'visible' => $this->isVisible,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
