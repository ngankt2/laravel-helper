<?php
namespace Ngankt2\LaravelHelper\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Ngankt2\LaravelHelper\ZiSecurity;

class EncryptEmailSingle implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return bool|array|string|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): bool|array|string|null
    {
        return ZiSecurity::decryptEmail($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param string $key
     * @param array $value
     * @param array $attributes
     * @return string|null
     */
    public function set(Model $model, string $key, $value, array $attributes): ?string
    {
        if (is_string($value) && $value) {
            return ZiSecurity::encryptEmail($value);
        }
        return null;

    }
}
