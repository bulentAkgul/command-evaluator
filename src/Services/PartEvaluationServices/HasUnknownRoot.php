<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;

class HasUnknownRoot extends Evaluator
{
    const KEY = 'root';
    const TYPE = 'unknown';

    public static function handle(array $request): ?array
    {
        if (parent::isStandalone()) return null;

        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, null, false, SetUnmodifiedMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        return !Arry::find(Settings::roots(), $request['root'], 'folder')
            && !Arry::has($request['root'], Settings::roots());
    }
}
