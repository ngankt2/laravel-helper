<?php

namespace Ngankt2\LaravelHelper\Traits;

use Illuminate\Support\Str;

trait HasFormInputExt
{
    public function hintWithRemainChars($maxLength, $reactive = true): static
    {
        $this->hint(function (?string $state) use ($maxLength): string {
            return (string) Str::of(mb_strlen($state))
                ->append(' / ')
                ->append($maxLength.' ')
                ->append(Str::of(__('Ký tự'))->lower());
        });

        if ($reactive) {
            $this->live(onBlur: true);
        }

        return $this;
    }
}

