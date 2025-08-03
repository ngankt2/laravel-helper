<?php

namespace Ngankt2\LaravelHelper;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

error_reporting(0);

class ZiSecurity
{
    /**
     * @param $string
     * @return bool|string
     */
    static function encryptString($string): bool|string
    {
        try {
            return Crypt::encryptString($string);
        } catch (DecryptException $e) {
            //
        }
        return false;
    }

    /**
     * @param $encryptedValue
     * @return bool|string
     */
    static function decryptString($encryptedValue): bool|string
    {
        try {
            return Crypt::decryptString($encryptedValue);
        } catch (DecryptException $e) {
            //
        }
        return false;
    }

    #endregion các phương thức liên quan đến mã hóa mật khẩu

    private static function _encryptDecryptDataInDb($plaintext, $key_config, $decrypt = false): bool|string
    {
        $cipher     = 'AES-128-CBC';
        $secret_key = sha1(env('ENCRYPT_' . $key_config, env('DB_DATABASE')));

        $key   = hash('sha256', $key_config . $secret_key . $cipher);
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv    = substr($key, 10, $ivlen);
        if ($decrypt) {
            return openssl_decrypt(($plaintext), $cipher, $key, 0, $iv);
        }
        return openssl_encrypt($plaintext, $cipher, $key, 0, $iv);

    }

    #region encrypt Email, Phone trước khi store vào Db
    static function encryptEmail($email): bool|string
    {
        $email = preg_replace('/\s+/', '', $email);
        $email = strtolower($email);
        return self::_encryptDecryptDataInDb($email, 'DB_EMAIL');
    }

    static function decryptEmail($ciphertext): bool|string
    {
        return self::_encryptDecryptDataInDb($ciphertext, 'DB_EMAIL', true);
    }

    static function encryptPhone($phone): bool|string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        return self::_encryptDecryptDataInDb($phone, 'DB_PHONE');
    }

    static function decryptPhone($ciphertext): bool|string
    {
        return self::_encryptDecryptDataInDb($ciphertext, 'DB_PHONE', true);
    }

    static function encryptOther($plaintext): bool|string
    {
        return self::_encryptDecryptDataInDb($plaintext, 'DB_OTHER');
    }

    static function decryptOther($ciphertext): bool|string
    {
        return self::_encryptDecryptDataInDb($ciphertext, 'DB_OTHER', true);
    }

    /**
     * Chức năng giúp tạo ra 1 id unique cho bảng nào đó, ID này sử dụng cho các trường hợp link xóa, edit, hoặc các action nhạy cảm
     * giúp giấu được ID dạng số của table,
     * @param $id
     * @param $table
     */
    static function buildSID($id, $table): string
    {
        $string = $id . '@sita@' . $table;
        return base64_encode(self::encryptOther($string));

    }

    /**
     * @param $sid
     * @param mixed $table : mặc định = false là k xác minh, nếu có sẽ là string table sẽ là tên table hoặc string dùng để build SID trước đó
     * @param bool $returnIdOnly
     * @return string|array|bool
     */
    static function getIDFromSID($sid, $table = '', bool $returnIdOnly = true): string|array|bool
    {
        $ciphertext = base64_decode($sid);

        if ($ciphertext) {
            $plaintext = self::decryptOther($ciphertext);

            $plaintextObj = explode('@sita@', $plaintext);
            if (isset($plaintextObj[1])) {
                if ($table) {
                    if ($table != $plaintextObj[1]) {
                        return false;
                    }
                }
                if ($returnIdOnly) {
                    return $plaintextObj[0];
                }
                return [
                    'id'    => $plaintextObj[0],
                    'table' => $plaintextObj[1]
                ];
            }
        }
        return false;
    }


    static function buildTokenWithSession($id): string
    {
        return sha1($id . 'sakura' . $id . session()->getId());
    }

    static function validateTokenWithSession($token, $id)
    {
        if (self::buildTokenWithSession($id) == $token) {
            return $id;
        }

        return FALSE;
    }

    private static array $encode_table
        = [
            '0' => 'j4p0k3',
            '1' => 'u8jo0e',
            '2' => 'g7d5c6',
            '3' => 'j9l0d2',
            '4' => 'm6o3p5',
            '5' => 'p2ruj8',
            '6' => 's4u901',
            '7' => 'v5x265',
            '8' => 'y6kyh8',
            '9' => 'kl67ya'
        ];

    public static function encode_number_basic($number): string
    {

        return implode('-', array_map(function ($digit) {
            //return $digit;
            return self::$encode_table[$digit];
        }, str_split($number)));
    }

    public static function decode_string_basic($encoded_string): int
    {
        $decoded_number = '';
        $codes          = explode('-', $encoded_string);
        foreach ($codes as $code) {
            foreach (self::$encode_table as $digit => $value) {
                if ($value == $code) {
                    $decoded_number .= $digit;
                    break;
                }
            }
        }
        return (int)$decoded_number;
    }
}
