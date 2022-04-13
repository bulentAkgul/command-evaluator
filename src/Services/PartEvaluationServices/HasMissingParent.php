<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\FindParentIssue;
use Bakgul\Evaluator\Tasks\SetParentMessage;

class HasMissingParent extends Evaluator
{
    const KEY = 'parent';
    const TYPE = 'missing';

    private static $issue;

    public static function handle(array $request): ?array
    {
        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, self::$issue, true, SetParentMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        self::findIssues($request['type']);

        return !$request[self::KEY] && !empty(self::$issue);
    }

    private static function findIssues(string $type): void
    {
        self::$issue = (new FindParentIssue)($type);
    }
}
