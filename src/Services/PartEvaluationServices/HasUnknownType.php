<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Isolation;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\HandlePartEvaluation;
use Bakgul\Evaluator\Tasks\SetDefaultMessage;
use Illuminate\Support\Arr;

class HasUnknownType extends Evaluator
{
    const KEY = 'type';
    const TYPE = 'unknown';

    private static $issue;

    public static function handle(array $request): ?array
    {
        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, self::$issue, false, SetDefaultMessage::class];
    }

    public static function hasIssue(array $request): bool
    {
        self::findIssues($request['type']);

        return !empty(self::$issue);
    }

    private static function findIssues($type)
    {
        self::$issue = array_diff(
            Arr::pluck(Isolation::types($type), 0),
            array_keys(Settings::files())
        );
    }
}
