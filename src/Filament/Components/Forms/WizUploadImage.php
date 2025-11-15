<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use function App\Filament\Components\str;

class WizUploadImage extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->getUploadedFileNameForStorageUsing(
            function (TemporaryUploadedFile $file) {
                $name = substr(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)), 0, 50);
                $name .= '-'.Str::random(6).'-'.Str::random(6);

                return str($name.'.'.$file->getClientOriginalExtension())->lower();
            },
        )
            ->disk('custom')
            ->visibility('private');
    }
}
