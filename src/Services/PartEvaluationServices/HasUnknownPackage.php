<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Package;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;

class HasUnknownPackage extends Evaluator
{
    const KEY = 'package';
    const TYPE = 'unknown';

    private static $confirmations;

    public static function handle(array $request, array $confirmations = []): ?array
    {
        if (!Arry::get($request, 'package') || parent::isStandalone()) return null;

        self::$confirmations = $confirmations;

        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [
            self::KEY,
            self::TYPE,
            parent::packageRequest($request),
            null,
            self::confirmable($request),
            SetUnmodifiedMessage::class
        ];
    }

    private static function confirmable($request): bool
    {
        return !str_contains($request['command'], 'relation');
    }

    public static function hasIssue(array $request): bool
    {
        if (self::isConfirmed($request)) return false;
        
        foreach (explode(',', $request['package']) as $package) {
            if (!Package::root($package)) return true;
        }

        return false;
    }

    private static function isConfirmed($request)
    {
        return self::confirmable($request)
            && in_array(self::KEY . '.' . self::TYPE, self::$confirmations);
    }
}
