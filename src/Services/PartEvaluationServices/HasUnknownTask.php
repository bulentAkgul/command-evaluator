<?php

namespace Bakgul\Evaluator\Services\PartEvaluationServices;

use Bakgul\Evaluator\Evaluator;
use Bakgul\Evaluator\Tasks\SetDefaultMessage;
use Bakgul\Kernel\Functions\CollectTypes;
use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Isolation;
use Illuminate\Support\Arr;

class HasUnknownTask extends Evaluator
{
    const KEY = 'task';
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
        self::findIssues($request);

        return !empty(self::$issue);
    }

    private static function findIssues($request)
    {
        self::$issue = array_filter(array_diff(self::isolateTasks($request), self::getTasks($request)));
    }

    private static function isolateTasks(array $request): array
    {
        return array_filter(self::produce(
            self::produce(
                Isolation::chunk($request['name']),
                fn ($x) => explode(',', $x)
            ),
            fn ($x) => Isolation::tasks($x)
        ));
    }

    private static function getTasks(array $request)
    {
        return self::produce(
            self::getTypes($request['type']),
            fn ($x) => Settings::files("{$x}.tasks")
        );
    }

    private static function getTypes(string $type): array
    {
        return Arry::unique(array_map(
            fn ($x) => $x['type'],    
            Arr::flatten(CollectTypes::_($type), 1)
        ));
    }

    private static function produce(array $array, callable $callback)
    {
        return Arry::unique(array_filter(Arr::flatten(array_map($callback, $array))));
    }
}
