<?php

namespace Ngankt2\LaravelHelper\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Ngankt2\LaravelHelper\AesDeterministic;

class AesDeterministicCast implements CastsAttributes
{
    protected AesDeterministic $crypto;
    protected string $context;

    public function __construct(string $context = '')
    {
        $secret = env('DB_OTHER_ENCRYPT_KEY');
        $this->crypto = new AesDeterministic($secret);
        $this->context = $context;
    }

    /**
     * Cast (DB -> PHP)
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (! $value) {
            return null;
        }

        return $this->crypto->decrypt($value);
    }

    /**
     * Set (PHP -> DB)
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_string($value) && $value !== '') {
            return $this->crypto->encrypt($value, $this->context);
        }
        return null;
    }
}
