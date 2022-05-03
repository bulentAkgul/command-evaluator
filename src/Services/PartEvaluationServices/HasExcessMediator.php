<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;

class HasExcessMediator extends Evaluator
{
    const KEY = 'relation';
    const TYPE = 'excess';

    private static $issue;
    private static $confirmations;

    public static function handle(array $request, array $confirmations): ?array
    {
        if ($request['relation'] != 'mtm') return null;

        self::$confirmations = $confirmations;

        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, self::$issue, true, SetUnmodifiedMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        return in_array(self::KEY . '.' . self::TYPE, self::$confirmations)
            ? false
            : $request['polymorphic'] && $request['mediator'];
    }
}
