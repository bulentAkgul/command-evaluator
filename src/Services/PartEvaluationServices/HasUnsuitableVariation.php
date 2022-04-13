<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Isolation;
use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\HandlePartEvaluation;
use Bakgul\Evaluator\Tasks\SetDefaultMessage;
use Illuminate\Support\Arr;

class HasUnsuitableVariation extends Evaluator
{
    const KEY = 'variation';
    const TYPE = 'unknown';

    private static $issue = [];

    public static function handle(array $request): ?array
    {
        return parent::evaluatePart(get_called_class(), $request);
    }

    public static function args($request): array
    {
        return [self::KEY, self::TYPE, $request, self::setIssue(), false, SetDefaultMessage::class];
    }

    private static function setIssue()
    {
        return Arry::unique(array_filter(self::$issue));
    }

    public static function hasIssue(array $request): bool
    {
        self::findIssues($request['type']);

        return !empty(self::$issue);
    }

    private static function findIssues($type)
    {
        self::$issue = Arry::unique(array_filter(
            [...self::$issue, ...self::issuedVariations($type)]
        ));
    }

    private static function issuedVariations(string $type)
    {
        return Arr::flatten(array_map(
            fn ($x) => self::isValid($x) ? '' : $x[1],
            Isolation::types($type)
        ));
    }

    private static function isValid(array $type): bool
    {
        return in_array($type[1], Settings::files("{$type[0]}.variations"));
    }
}
