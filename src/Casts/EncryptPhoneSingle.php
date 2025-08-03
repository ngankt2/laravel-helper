<?php
namespace Ngankt2\LaravelHelper\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Ngankt2\LaravelHelper\ZiSecurity;


class EncryptPhoneSingle implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param string $key
     * @param  mixed  $value
     * @param array $attributes
     * @return array|false|string
     */
    public function get(\Illuminate\Database\Eloquent\Model $model, string $key, mixed $value, array $attributes): bool|array|string|null
    {
        return ZiSecurity::decryptPhone($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param string $key
     * @param  array  $value
     * @param array $attributes
     * @return string
     */
    public function set(\Illuminate\Database\Eloquent\Model $model, string $key, $value, array $attributes): ?string
    {
        if(is_string($value) && $value){
            return ZiSecurity::encryptPhone($value);
        }
        return null;

    }
}
