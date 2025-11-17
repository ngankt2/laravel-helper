<?php

namespace Ngankt2\LaravelHelper;


use InvalidArgumentException;
use RuntimeException;

class AesDeterministic
{
    private string $key;
    private string $pepper;

    public function __construct(string $envKey)
    {
        if (empty($envKey)) {
            throw new InvalidArgumentException("DB_OTHER_ENCRYPT_KEY is required");
        }

        $this->pepper = $envKey;
        // derive AES-256 key (sha256 -> 32 bytes)
        $this->key = hash('sha256', $envKey, true);
    }

    /**
     * Derive deterministic 12-byte IV from plaintext + optional context
     */
    private function deriveIvFromPlaintext(string $plain, string $context = ''): string
    {
        $hmac = hash_hmac('sha256', $context . "|" . $plain, $this->pepper, true);
        return substr($hmac, 0, 12); // 12 bytes IV
    }

    /**
     * Encrypt deterministic (base64: iv(12) + tag(16) + ciphertext)
     */
    public function encrypt(string $plain, string $context = ''): string
    {
        $iv = $this->deriveIvFromPlaintext($plain, $context);

        $ciphertext = openssl_encrypt(
            $plain,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            "", // no AAD
            16   // tag length
        );

        if ($ciphertext === false) {
            throw new RuntimeException("Encryption failed: " . openssl_error_string());
        }

        return base64_encode($iv . $tag . $ciphertext);
    }

    /**
     * Decrypt deterministic payload (base64: iv + tag + ciphertext)
     */
    public function decrypt(string $payloadBase64): string
    {
        $buf = base64_decode($payloadBase64, true);
        if ($buf === false || strlen($buf) < 12 + 16) {
            throw new InvalidArgumentException("Invalid payload");
        }

        $iv = substr($buf, 0, 12);
        $tag = substr($buf, 12, 16);
        $ciphertext = substr($buf, 28);

        $plain = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            "" // no AAD
        );

        if ($plain === false) {
            throw new RuntimeException("Decryption failed: " . openssl_error_string());
        }

        return $plain;
    }
}
