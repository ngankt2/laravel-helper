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

class WizUploadImageOptimize extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->getUploadedFileNameForStorageUsing(
            function (TemporaryUploadedFile $file) {
                $name = substr(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)), 0, 50);
                $name .= '-'.Str::random(6).'-'.Str::random(6);

                return Str::of($name.'.'.$file->getClientOriginalExtension())->lower();
            },
        )
            ->disk('custom')
            ->visibility('private');

        // Tự động convert ảnh sang WebP trước khi upload
        $this->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
            $folder = preg_replace(
                '#/+#', // tìm mọi chuỗi có 1 hoặc nhiều dấu "/"
                '/',
                (Filament::getTenant()?->slug ?? 'shared').date('/Y/m')
            );
            $originalName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = strtolower($file->getClientOriginalExtension());
            $mime = $file->getMimeType();

            // Chọn driver phù hợp
            $driver = extension_loaded('imagick') ? ImagickDriver::class : GdDriver::class;
            $manager = new ImageManager(new $driver);

            // Nếu là ảnh -> convert sang WebP
            if (Str::startsWith($mime, 'image/')) {
                $image = $manager->read($file->getPathname());

                // Encode sang WebP với chất lượng 90
                $encoded = $image->toWebp(90);

                // Tạo tên file webp
                $filename = $originalName.'-'.Str::random(6).'-'.Str::random(6).'.webp';
                $path = "{$folder}/{$filename}";

                Storage::disk('custom')->put($path, (string) $encoded, [
                    'visibility' => 'public',
                    'ContentType' => 'image/webp',
                ]);

                return $path;
            }

            // Nếu không phải ảnh, upload nguyên bản
            $filename = $originalName.'-'.Str::random(6).'-'.Str::random(6).'.'.$extension;
            $path = "{$folder}/{$filename}";

            Storage::disk('custom')->putFileAs($folder, $file, $filename, [
                'visibility' => 'public',
            ]);

            return $path;
        });
    }
}
