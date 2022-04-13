<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Package;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;

class HasDuplicatedPackage extends Evaluator
{
    const KEY = 'package';
    const TYPE = 'duplicated';

    public static function handle(array $request): ?array
    {
        if (!$request['package'] || parent::isStandalone()) return null;
        
        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, null, false, SetUnmodifiedMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        return in_array($request['package'], Package::list());
    }
}
