<?php

namespace Ngankt2\LaravelHelper\Casts;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SafeEnumCast implements CastsAttributes
{
    public function __construct(
        private string $enumClass
    ) {}

    public function get($model, string $key, $value, array $attributes)
    {
        return $this->enumClass::tryFrom($value) ?? $value;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value instanceof $this->enumClass ? $value->value : $value;
    }
}
