<?php

namespace Bakgul\Evaluator\Tasks;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Text;

class GetMessage
{
    private static $key;

    public static function _(string $key, array $request): string
    {
        self::key($key);

        return self::set(self::message($key, self::modifier($request)), $request);
    }

    private static function key(string $key): void
    {
        self::$key = explode(Settings::seperators('addition'), $key)[0];
    }

    private static function modifier(array $request): ?string
    {
        return Arry::get(explode(Settings::seperators('modifier'), $request['command']), 1);
    }

    private static function set(string $message, array $request): string
    {
        return Text::replaceByMap(
            self::map(self::placeholders($message), $request),
            $message
        );
    }

    private static function message(string $key, ?string $modifier): string
    {
        return is_array(Settings::messages($key))
            ? Settings::messages("{$key}.{$modifier}") ?? self::default()
            : Settings::messages($key) ?? self::default();
    }

    private static function default()
    {
        return Settings::messages('default.reasonless');
    }

    private static function placeholders(string $message): array
    {
        return Text::allBetweens($message, '{{', '}}');
    }

    private static function map($placeholders, $request)
    {
        return array_merge(
            ['key' => self::$key],
            array_combine(
                $placeholders,
                self::mapValues($placeholders, $request)
            )
        );
    }

    private static function mapValues(array $placeholders, array $request): array
    {
        return array_map(fn ($x) => Arry::get($request, $x) ?? '', $placeholders);
    }
}
