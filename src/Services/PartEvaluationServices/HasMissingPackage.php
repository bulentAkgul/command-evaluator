<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;

class HasMissingPackage extends Evaluator
{
    const KEY = 'package';
    const TYPE = 'missing';

    private static $confirmations;

    public static function handle(array $request, array $confirmations): ?array
    {
        if (parent::isStandalone() || parent::isWarningsDisabled()) return null;

        self::$confirmations = $confirmations;

        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, parent::packageRequest($request), null, true, SetUnmodifiedMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        return !in_array(self::KEY . '.' . self::TYPE, self::$confirmations)
            && !$request['package'];
    }
}
