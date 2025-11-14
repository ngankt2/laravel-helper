<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Filament\Forms\Components\TextInput;

class WizMoneyInput
{
    public static function make($field): TextInput
    {
        return TextInput::make($field)
            ->numeric()
            ->currencyMask(
                thousandSeparator: '.',   // Dấu chấm ngăn cách hàng nghìn
                decimalSeparator: ',',    // Dấu phẩy ngăn cách thập phân
                precision: 0,             // Số lẻ thập phân (0 nếu tiền VNĐ)
            )
            ->suffix('₫');
    }
}
