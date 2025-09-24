<?php

namespace Ngankt2\LaravelHelper\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class StrictEnumArrayCast implements CastsAttributes
{
    public function __construct(
        protected string $enumClass // Enum cần enforce
    ) {}

    public function get($model, string $key, $value, array $attributes): array
    {
        $decoded = json_decode($value, true) ?? [];

        if (! is_array($decoded)) {
            return [];
        }

        $valid = array_map(fn ($case) => $case->value, $this->enumClass::cases());

        // Trả về chỉ các giá trị hợp lệ
        return array_values(array_intersect($decoded, $valid));
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if (! is_array($value)) {
            $value = [];
        }

        // Convert tất cả phần tử về string (nếu là enum thì lấy ->value)
        $value = array_map(function ($item) {
            if ($item instanceof \BackedEnum) {
                return $item->value;
            }
            return (string) $item;
        }, $value);

        $valid = array_map(fn ($case) => $case->value, $this->enumClass::cases());

        // Chỉ giữ lại giá trị hợp lệ
        $cleaned = array_values(array_intersect($value, $valid));

        return json_encode($cleaned);
    }

}
