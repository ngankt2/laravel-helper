<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Tables;

use Filament\Tables\Columns\TextInputColumn;

class WizMoneyInputColumn
{
    public static function make($field): TextInputColumn
    {
        return TextInputColumn::make($field)
            ->currencyMask(
                thousandSeparator: '.',   // Dấu chấm ngăn cách hàng nghìn
                decimalSeparator: ',',    // Dấu phẩy ngăn cách thập phân
                precision: 0,             // Số lẻ thập phân (0 nếu tiền VNĐ)
            )
            ->suffix('₫');
    }
}
