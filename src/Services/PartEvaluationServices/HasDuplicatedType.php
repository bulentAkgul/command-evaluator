<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Isolation;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetUnmodifiedMessage;
use Illuminate\Support\Arr;

class HasDuplicatedType extends Evaluator
{
    const KEY = 'type';
    const TYPE = 'duplicated';

    private static $issue;

    public static function handle(array $request): ?array
    {
        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request)
    {
        return [self::KEY, self::TYPE, $request, self::$issue, false, SetUnmodifiedMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        self::findIssues($request['type']);

        return count(self::$issue) > 0;
    }

    private static function findIssues(string $type)
    {
        self::$issue = array_keys(array_filter(
            array_count_values(Arr::pluck(Isolation::types($type), 0)),
            fn ($x) => $x > 1,
        ));
    }
}
