<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\FindParentIssue;
use Bakgul\Evaluator\Tasks\SetParentMessage;

class HasConflictingParent extends Evaluator
{
    const KEY = 'parent';
    const TYPE = 'conflicting';

    private static $issue;

    public static function handle(array $request): ?array
    {
        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request)
    {
        return [self::KEY, self::TYPE, $request, self::$issue, false, SetParentMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        self::findIssues($request['type']);

        return count(self::$issue) > 1;
    }

    private static function findIssues(string $type)
    {
        self::$issue = (new FindParentIssue)($type);
    }
}
