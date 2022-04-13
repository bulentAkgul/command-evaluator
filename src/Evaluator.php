<?php

namespace Bakgul\Evaluator;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Settings;

class Evaluator
{
    public static function isStandalone()
    {
        return Settings::standalone();
    }

    public static function evaluatePart($evaluator, $request): ?array
    {
        return $evaluator::hasIssue($request) ? self::getIssue($evaluator::args($request)) : null;
    }

    public static function getIssue($args): array
    {
        [$key, $type, $request, $issue, $confirm, $messanger] = $args;

        return [
            'key' => $key,
            'evaluated' => $type,
            'is_confirmable' => $confirm,
            'problem' => $confirm ? $request[$key] : '',
            'message' => (new $messanger)($key, $type, $request, $issue)
        ];
    }

    protected static function packageRequest($request): array
    {
        $family = self::family(Arry::get($request, 'type'));

        $request['path'] = $family == 'src' ? 'app' : $family;

        return $request;
    }

    private static function family(?string $type)
    {
        return $type ? Settings::files("{$type}.family") : 'src';
    }
}
