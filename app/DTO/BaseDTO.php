<?php

declare(strict_types=1);

namespace App\DTO;

use BackedEnum;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionProperty;
use UnitEnum;

abstract class BaseDTO
{
    final public static function fromRequest(Request $request): static
    {
        $reflection = new ReflectionClass(static::class);
        $properties = collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
            ->mapWithKeys(function (ReflectionProperty $property) use ($request) {
                $name = $property->getName();
                $value = $request->input($name);

                // Handle type conversion based on property type
                if ($property->hasType()) {
                    $type = $property->getType();
                    if (! $type->isBuiltin()) {
                        $className = $type->getName();
                        if (enum_exists($className)) {
                            $value = $className::tryFrom($value);
                        }
                    }
                }

                return [$name => $value];
            })
            ->filter(fn ($value) => $value !== null)
            ->toArray();

        return new static(...$properties);
    }

    final public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if (! $constructor) {
            return new static();
        }

        $parameters = collect($constructor->getParameters())
            ->mapWithKeys(function ($parameter) use ($data) {
                $name = $parameter->getName();
                $value = $data[$name] ?? null;

                // Handle default values
                if ($value === null && $parameter->isDefaultValueAvailable()) {
                    $value = $parameter->getDefaultValue();
                }

                // Handle type conversion
                if ($parameter->hasType() && $value !== null) {
                    $type = $parameter->getType();
                    if (! $type->isBuiltin()) {
                        $className = $type->getName();
                        if (enum_exists($className)) {
                            $value = $className::tryFrom($value);
                        }
                    }
                }

                return [$name => $value];
            });

        return new static(...$parameters->toArray());
    }

    final public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $result = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            $value = $property->getValue($this);

            if ($value instanceof BackedEnum) {
                $value = $value->value;
            } elseif ($value instanceof UnitEnum) {
                $value = $value->name;
            }

            $result[$name] = $value;
        }

        return $result;
    }

    final public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    final public function only(array $keys): array
    {
        return array_intersect_key($this->toArray(), array_flip($keys));
    }

    final public function except(array $keys): array
    {
        return array_diff_key($this->toArray(), array_flip($keys));
    }
}
