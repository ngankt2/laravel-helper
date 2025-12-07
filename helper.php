<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('_get_max_upload_support')) {
    /**
     * @param float|int $max is Kilobyte
     * @return string|int|float
     */
    function _get_max_upload_support(float|int $max = 2 * 1024): string|int|float
    {

        $serverMaxSize = return_kilobytes(ini_get('upload_max_filesize'));
        if ($max > $serverMaxSize) {
            return $serverMaxSize;
        }
        return $max;
    }
}
if (!function_exists('return_kilobytes')) {
    /**
     * @param float|int $max is Kilobyte
     * @return string|int|float
     */
    function return_kilobytes($val): int|string
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        return match ($last) {
            'g' => (float)$val * 1024 * 1024,
            'm' => (float)$val * 1024,
            default => (float)$val,
        };
    }
}

if (!function_exists('zi_format_currency')) {

    function zi_format_currency($amount, $symbol = '₫'): string
    {
        return number_format($amount, 0, ',', '.') . ' <sup class="zi-currency-symbol">' . $symbol . '</sup>';
    }
}

if (!function_exists('zi_format_currency_vnd')) {

    function zi_format_currency_vnd($amount): string
    {
        return number_format($amount, 0, ',', '.');
    }
}


if (!function_exists('zi_language_name')) {

    function zi_language_name($language_code): string
    {
        // Danh sách ánh xạ ngôn ngữ và tên ngôn ngữ
        $language_names = [
            'en' => 'English',
            'vi' => 'Vietnamese',
            'fr' => 'French',
            'de' => 'German',
            'es' => 'Spanish',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'zh' => 'Chinese',
            'ru' => 'Russian',
            'pt' => 'Portuguese',
            'ar' => 'Arabic',
            'he' => 'Hebrew',
            'id' => 'Indonesian',
            'th' => 'Thai',
            'pl' => 'Polish',
            'nl' => 'Dutch',
            'sv' => 'Swedish',
            'tr' => 'Turkish',
            'cs' => 'Czech',
            'da' => 'Danish',
            'no' => 'Norwegian',
            'fi' => 'Finnish',
            'el' => 'Greek',
            'hr' => 'Croatian',
            'hu' => 'Hungarian',
            'sk' => 'Slovak',
            'ro' => 'Romanian',
            'sr' => 'Serbian',
            'bg' => 'Bulgarian',
            'uk' => 'Ukrainian',
            'bs' => 'Bosnian',
            'sq' => 'Albanian',
            'mk' => 'Macedonian',
            'ms' => 'Malay',
            'sw' => 'Swahili',
            'tl' => 'Filipino',
            'bn' => 'Bengali',
            'pa' => 'Punjabi',
            'gu' => 'Gujarati',
            'ml' => 'Malayalam',
            'te' => 'Telugu',
            'mr' => 'Marathi',
            'ne' => 'Nepali',
            'si' => 'Sinhala',
            'lo' => 'Lao',
            'km' => 'Khmer',
            'my' => 'Burmese',
            'eu' => 'Basque',
            'ca' => 'Catalan',
            'gl' => 'Galician',
            'wa' => 'Walloon',
            'cy' => 'Welsh',
            '_all' => 'All Languages',
        ];

        // Kiểm tra xem ngôn ngữ có trong danh sách không, nếu không thì trả về tên mặc định "Unknown"
        return $language_names[$language_code] ?? 'Unknown Language'; // Mặc định trả về "Unknown Language"
    }
}


if (!function_exists('zi_language')) {

    function zi_language($language_code): string
    {
        return zi_language_icon($language_code) . ' ' . zi_language_name($language_code);
    }
}

if (!function_exists('zi_language_icon')) {

    function zi_language_icon($language_code): string
    {
        // Danh sách ánh xạ ngôn ngữ và emoji
        $language_emojis = [
            'en' => '🇬🇧', // English
            'vi' => '🇻🇳', // Vietnamese
            'fr' => '🇫🇷', // French
            'de' => '🇩🇪', // German
            'es' => '🇪🇸', // Spanish
            'it' => '🇮🇹', // Italian
            'ja' => '🇯🇵', // Japanese
            'ko' => '🇰🇷', // Korean
            'zh' => '🇨🇳', // Chinese
            'ru' => '🇷🇺', // Russian
            'pt' => '🇵🇹', // Portuguese
            'ar' => '🇸🇦', // Arabic
            'he' => '🇮🇱', // Hebrew
            'id' => '🇮🇩', // Indonesian
            'th' => '🇹🇭', // Thai
            'pl' => '🇵🇱', // Polish
            'nl' => '🇳🇱', // Dutch
            'sv' => '🇸🇪', // Swedish
            'tr' => '🇹🇷', // Turkish
            'cs' => '🇨🇿', // Czech
            'da' => '🇩🇰', // Danish
            'no' => '🇳🇴', // Norwegian
            'fi' => '🇫🇮', // Finnish
            'el' => '🇬🇷', // Greek
            'hr' => '🇭🇷', // Croatian
            'hu' => '🇭🇺', // Hungarian
            'sk' => '🇸🇰', // Slovak
            'ro' => '🇷🇴', // Romanian
            'sr' => '🇷🇸', // Serbian
            'bg' => '🇧🇬', // Bulgarian
            'uk' => '🇺🇦', // Ukrainian
            'bs' => '🇧🇦', // Bosnian
            'sq' => '🇦🇱', // Albanian
            'mk' => '🇲🇰', // Macedonian
            'ms' => '🇲🇾', // Malay
            'sw' => '🇰🇪', // Swahili
            'tl' => '🇵🇭', // Filipino
            'bn' => '🇧🇩', // Bengali
            'pa' => '🇮🇳', // Punjabi
            'gu' => '🇮🇳', // Gujarati
            'ml' => '🇮🇳', // Malayalam
            'te' => '🇮🇳', // Telugu
            'mr' => '🇮🇳', // Marathi
            'ne' => '🇳🇵', // Nepali
            'si' => '🇱🇰', // Sinhala
            'lo' => '🇱🇸', // Lao
            'km' => '🇰🇭', // Khmer
            'my' => '🇲🇲', // Burmese
            'eu' => '🇪🇸', // Basque
            'ca' => '🇪🇸', // Catalan
            'gl' => '🇪🇸', // Galician
            'wa' => '🇧🇪', // Walloon
            'cy' => '🇬🇧', // Welsh
        ];

        // Kiểm tra xem ngôn ngữ có trong danh sách không, nếu không thì trả về emoji mặc định
        return $language_emojis[$language_code] ?? '🌍'; // Mặc định trả về emoji của Trái Đất
    }
}

if (!function_exists('zi_all_languages')) {
    /**
     * Returns an array of all languages with their properties.
     *
     * @return array An array of languages, each with code, name, icon, and other properties.
     */
    function zi_all_languages(): array
    {
        $languages = zi_get_languages();
        $result = [];
        foreach ($languages as $code => $properties) {
            $result[] = [
                'code' => $code,
                'name' => $properties['name'],
                'icon' => $properties['icon'],
            ];
        }
        return $result;
    }
}

if (!function_exists('zi_get_languages')) {
    /**
     * Returns an array of all languages with their properties.
     *
     * @return array An array of languages, each with code, name, and icon.
     */
    function zi_get_languages(?array $onlyKeys = null): array
    {
        $all = [
            'en' => ['name' => 'English', 'icon' => '🇬🇧'],
            'vi' => ['name' => 'Vietnamese', 'icon' => '🇻🇳'],
            'fr' => ['name' => 'French', 'icon' => '🇫🇷'],
            'de' => ['name' => 'German', 'icon' => '🇩🇪'],
            'es' => ['name' => 'Spanish', 'icon' => '🇪🇸'],
            'it' => ['name' => 'Italian', 'icon' => '🇮🇹'],
            'ja' => ['name' => 'Japanese', 'icon' => '🇯🇵'],
            'ko' => ['name' => 'Korean', 'icon' => '🇰🇷'],
            'zh' => ['name' => 'Chinese', 'icon' => '🇨🇳'],
            'ru' => ['name' => 'Russian', 'icon' => '🇷🇺'],
            'pt' => ['name' => 'Portuguese', 'icon' => '🇵🇹'],
            'ar' => ['name' => 'Arabic', 'icon' => '🇸🇦'],
            'he' => ['name' => 'Hebrew', 'icon' => '🇮🇱'],
            'id' => ['name' => 'Indonesian', 'icon' => '🇮🇩'],
            'th' => ['name' => 'Thai', 'icon' => '🇹🇭'],
            'pl' => ['name' => 'Polish', 'icon' => '🇵🇱'],
            'nl' => ['name' => 'Dutch', 'icon' => '🇳🇱'],
            'sv' => ['name' => 'Swedish', 'icon' => '🇸🇪'],
            'tr' => ['name' => 'Turkish', 'icon' => '🇹🇷'],
            'cs' => ['name' => 'Czech', 'icon' => '🇨🇿'],
            'da' => ['name' => 'Danish', 'icon' => '🇩🇰'],
            'no' => ['name' => 'Norwegian', 'icon' => '🇳🇴'],
            'fi' => ['name' => 'Finnish', 'icon' => '🇫🇮'],
            'el' => ['name' => 'Greek', 'icon' => '🇬🇷'],
            'hr' => ['name' => 'Croatian', 'icon' => '🇭🇷'],
            'hu' => ['name' => 'Hungarian', 'icon' => '🇭🇺'],
            'sk' => ['name' => 'Slovak', 'icon' => '🇸🇰'],
            'ro' => ['name' => 'Romanian', 'icon' => '🇷🇴'],
            'sr' => ['name' => 'Serbian', 'icon' => '🇷🇸'],
            'bg' => ['name' => 'Bulgarian', 'icon' => '🇧🇬'],
            'uk' => ['name' => 'Ukrainian', 'icon' => '🇺🇦'],
            'bs' => ['name' => 'Bosnian', 'icon' => '🇧🇦'],
            'sq' => ['name' => 'Albanian', 'icon' => '🇦🇱'],
            'mk' => ['name' => 'Macedonian', 'icon' => '🇲🇰'],
            'ms' => ['name' => 'Malay', 'icon' => '🇲🇾'],
            'sw' => ['name' => 'Swahili', 'icon' => '🇰🇪'],
            'tl' => ['name' => 'Filipino', 'icon' => '🇵🇭'],
            'bn' => ['name' => 'Bengali', 'icon' => '🇧🇩'],
            'pa' => ['name' => 'Punjabi', 'icon' => '🇮🇳'],
            'gu' => ['name' => 'Gujarati', 'icon' => '🇮🇳'],
            'ml' => ['name' => 'Malayalam', 'icon' => '🇮🇳'],
            'te' => ['name' => 'Telugu', 'icon' => '🇮🇳'],
            'mr' => ['name' => 'Marathi', 'icon' => '🇮🇳'],
            'ne' => ['name' => 'Nepali', 'icon' => '🇳🇵'],
            'si' => ['name' => 'Sinhala', 'icon' => '🇱🇰'],
            'lo' => ['name' => 'Lao', 'icon' => '🇱🇸'],
            'km' => ['name' => 'Khmer', 'icon' => '🇰🇭'],
            'my' => ['name' => 'Burmese', 'icon' => '🇲🇲'],
            'eu' => ['name' => 'Basque', 'icon' => '🇪🇸'],
            'ca' => ['name' => 'Catalan', 'icon' => '🇪🇸'],
            'gl' => ['name' => 'Galician', 'icon' => '🇪🇸'],
            'wa' => ['name' => 'Walloon', 'icon' => '🇧🇪'],
            'cy' => ['name' => 'Welsh', 'icon' => '🇬🇧'],
            '_all' => ['name' => 'All Languages', 'icon' => '🌍'],
        ];

        if (is_array($onlyKeys)) {
            return collect($all)->only($onlyKeys)->toArray();
        }

        return $all;
    }
}

if (!function_exists('zi_get_language_labels')) {
    /**
     * Trả về mảng [code => "🇻🇳 Vietnamese"] để hiển thị label
     *
     * @param array|null $onlyKeys
     * @return array
     */
    function zi_get_language_labels(?array $onlyKeys = null): array
    {
        return collect(zi_get_languages($onlyKeys))
            ->mapWithKeys(fn($lang, $code) => [
                $code => trim("{$lang['icon']} {$lang['name']}")
            ])
            ->toArray();
    }
}
if (!function_exists('zi_to_storage_url')) {

    function zi_to_storage_url(?string $path, $disk = 'custom'): ?string
    {
        if (!$path) {
            return null;
        }

        // Nếu là URL tuyệt đối => trả về luôn
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Trả về URL S3
        return Storage::disk($disk)->url($path);
    }
}
if (!function_exists('zi_get_gravatar_url')) {
    function zi_get_gravatar_url($email): string
    {
        // Trim leading and trailing whitespace from
        // an email address and force all characters
        // to lower case
        $address = strtolower(trim($email));

        // Create an SHA256 hash of the final string
        $hash = hash('sha256', $address);

        // Grab the actual image URL
        return 'https://gravatar.com/avatar/' . $hash;
    }
}


if (!function_exists('zi_generate_avatar')) {
    function zi_generate_avatar($text, $size = 64): string
    {
        // Tạo hash từ text để xác định màu sắc cố định
        $hash = md5($text);

        // Tạo màu nền từ hash (lấy 6 ký tự đầu để tạo mã màu hex)
        $bgColor = substr($hash, 0, 6);

        // Chuyển đổi màu nền sang RGB
        $r = hexdec(substr($bgColor, 0, 2));
        $g = hexdec(substr($bgColor, 2, 2));
        $b = hexdec(substr($bgColor, 4, 2));

        // Tính toán màu văn bản tương phản (đen hoặc trắng)
        // Dựa trên độ sáng của màu nền
        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
        $textColor = $brightness > 128 ? '000000' : 'ffffff';

        // Mã hóa tên để sử dụng trong URL
        $encodedText = urlencode($text);

        // Trả về URL của avatar được tạo bởi ui-avatars.com
        return "https://ui-avatars.com/api/?name={$encodedText}&size={$size}&background={$bgColor}&color={$textColor}";
    }
}





