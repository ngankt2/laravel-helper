<?php

namespace Ngankt2\LaravelHelper;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZiDebug
{
    // ======================================================================
    // COMMON FILTERS (Shared by both Lark & Telegram)
    // ======================================================================
    const exclude_messages = [
        'Unauthenticated.',
        'The given data was invalid.',
        'CSRF token mismatch.',
        'Too Many Attempts.',
        'xUnauthenticated.',
    ];

    const exclude_status_codes = [401, 403, 404, 405];

    const exclude_agents = ['TelegramBot', 'TwitterBot'];

    static array $larkFields = [];


    public static function setLarkFields($fields)
    {
        self::$larkFields[] = $fields;
    }


    // ======================================================================
    // SEND LARK (Use common filters)
    // ======================================================================
    public static function sendErrorToLark(
        string $larkUrl,
        \Throwable $exception,
        \Illuminate\Http\Request $request
    ): array {
        try {
            if (self::shouldSkipCommon($exception)) {
                return ['skipped' => true, 'reason' => 'Common filter skipped'];
            }

            $payload  = self::buildLarkMessage($exception, $request);

            $response = Http::post($larkUrl, $payload)->json();

            return [
                'success' => true,
                'lark'    => $response,
            ];

        } catch (\Throwable $ex) {
            Log::error("ZiDebug Lark failed: " . $ex->getMessage());
            return ['success' => false, 'error' => $ex->getMessage()];
        }
    }


    // ======================================================================
    // SEND LARK (Use common filters)
    // ======================================================================
    public static function sendMessageToLark(
        string $larkUrl,
        string $message,
    ): array {
        try {
            $payload = [
                'msg_type' => 'text',
                'content'  => [
                    'text' => $message,
                ],
            ];

            $response = Http::post($larkUrl, $payload)->json();

            return [
                'success' => true,
                'lark'    => $response,
            ];

        } catch (\Throwable $ex) {
            Log::error("ZiDebug Lark failed: " . $ex->getMessage());
            return ['success' => false, 'error' => $ex->getMessage()];
        }
    }


    // ======================================================================
    // SEND TELEGRAM (Use common filters)
    // ======================================================================
    public static function sendErrorToTelegram(
        string $botToken,
        string $chatId,
        \Throwable $exception,
        \Illuminate\Http\Request $request
    ): array {
        try {
            if (self::shouldSkipCommon($exception)) {
                return ['skipped' => true, 'reason' => 'Common filter skipped'];
            }

            $text = self::buildTelegramMessage($exception, $request);

            $response = Http::post(
                "https://api.telegram.org/bot{$botToken}/sendMessage",
                [
                    'chat_id'    => $chatId,
                    'text'       => $text,
                    'parse_mode' => 'Markdown',
                ]
            )->json();

            return [
                'success'  => true,
                'telegram' => $response,
            ];

        } catch (\Throwable $ex) {
            Log::error("ZiDebug Telegram failed: " . $ex->getMessage());
            return ['success' => false, 'error' => $ex->getMessage()];
        }
    }

    // ======================================================================
    // SEND TELEGRAM (Use common filters)
    // ======================================================================
    public static function sendMessageToTelegram(
        string $botToken,
        string $chatId,
        string $message
    ): array {
        try {
            $response = Http::post(
                "https://api.telegram.org/bot{$botToken}/sendMessage",
                [
                    'chat_id'    => $chatId,
                    'text'       => $message,
                    'parse_mode' => 'Markdown',
                ]
            )->json();

            return [
                'success'  => true,
                'telegram' => $response,
            ];

        } catch (\Throwable $ex) {
            Log::error("ZiDebug Telegram failed: " . $ex->getMessage());
            return ['success' => false, 'error' => $ex->getMessage()];
        }
    }


    // ======================================================================
    // FILTER (Shared)
    // ======================================================================
    protected static function shouldSkipCommon(\Throwable $e): bool
    {
        $message = trim($e->getMessage());
        $agent   = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $status  = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        return
            in_array($message, self::exclude_messages, true) ||
            str_contains($message, 'Unauthenticated') ||
            self::uaMatches($agent) ||
            in_array($status, self::exclude_status_codes, true);
    }


    protected static function uaMatches(string $agent): bool
    {
        foreach (self::exclude_agents as $needle) {
            if (str_contains($agent, $needle)) {
                return true;
            }
        }
        return false;
    }


    // ======================================================================
    // TELEGRAM MESSAGE BUILDER
    // ======================================================================
    protected static function buildTelegramMessage(\Throwable $e, $request): string
    {
        $escape = fn($str) => str_replace(['_', '*', '`'], ['\_', '\*', '\`'], $str);

        return
            "*🔥 ERROR ALERT*\n" .
            "*Message:* " . $escape($e->getMessage()) . "\n" .
            "*File:* `" . $escape($e->getFile()) . "`\n" .
            "*Line:* " . $e->getLine() . "\n" .
            "*URL:* `" . $escape($request->fullUrl()) . "`\n" .
            "*Method:* " . $request->method() . "\n" .
            "*User:* " . $escape(optional(auth()->user())->name ?? 'Guest') . "\n" .
            "*IP:* " . $request->ip() . "\n" .
            "*Time:* " . now()->format('Y-m-d H:i:s');
    }


    // ======================================================================
    // LARK MESSAGE BUILDER
    // ======================================================================
    protected static function buildLarkMessage(\Throwable $e, $request): array
    {
        $fields = [
            self::md("🚨 Exception", "`" . get_class($e) . "`"),
            self::md("💬 Message", $e->getMessage()),
            self::md("📁 File", "`" . $e->getFile() . "`"),
            self::md("📍 Line", (string)$e->getLine()),
            self::md("🌐 URL", "`{$request->method()} {$request->fullUrl()}`"),
            self::md("👤 User", optional(auth()->user())->name ?? 'Guest'),
            self::md("🖥️ IP", $request->ip()),
            self::md("⚙️ Env", app()->environment()),
            self::md("🕐 Time", now()->format('Y-m-d H:i:s')),
            self::md("UserAgent", $request->userAgent() ?? ''),
        ];

        foreach (self::$larkFields as $item) {
            foreach ($item as $key => $value) {
                $fields[] = self::md($key, $value);
            }
        }

        return [
            'msg_type' => 'interactive',
            'card'     => [
                'header'   => [
                    'title'    => [
                        'tag'     => 'plain_text',
                        'content' => '🔥 System Error Alert',
                    ],
                    'template' => 'red',
                ],
                'elements' => [
                    [
                        'tag'    => 'div',
                        'fields' => $fields,
                    ],
                ],
            ],
        ];
    }


    private static function md(string $label, string $content): array
    {
        return [
            'is_short' => false,
            'text'     => [
                'tag'     => 'lark_md',
                'content' => "**{$label}:** {$content}",
            ],
        ];
    }
}
